<?php
/**
 * Created by PhpStorm.
 * User: Xiao（229622916@qq.com）
 * Date: 2018/1/17
 * Time: 11:19
 * Intro: 上传类
 */
namespace app\admin\controller;

use think\Image;

class Upload extends Base
{

    public function pic()
    {
        $file = request()->file('picture');
        // 文件大小限制
        if (config('system_config')['upload_file_size'] > 0) {
            if ($file->getInfo()['size'] > config('system_config')['upload_file_size'] * 1024) {
                return json(array('code' => 0, 'msg' => '文件大小超过限制~'));
            }
        }

        if ($file) {
            $save_dir = ROOT_PATH . 'uploads' . DS . 'picture/';
            $pic_path = '/uploads/picture/';
            if (!is_dir($save_dir)) {
                mkdir($save_dir, 0766, true);
            }
            $info = $file->move($save_dir);
            if ($info) {
                if (strpos(config('system_config')['upload_image_ext'], $info->getExtension()) !== false) {
                    $new_file_path = $pic_path . str_replace('\\', '/', $info->getSaveName());

                    // 水印功能
                    if (config('system_config')['upload_thumb_water'] == 1 && config('system_config')['upload_thumb_water_pic'] > 0) {
                        $this->waterMark($save_dir . str_replace('\\', '/', $info->getSaveName()));
                    }

                    // 缩略图
                    if (config('system_config')['upload_image_thumb'] != '') {
                        $p['thumb'] = $this->makeThumb($save_dir . str_replace('\\', '/', $info->getSaveName()));
                    }

                    // 保存
                    $p['path'] = $new_file_path;
                    $p['name'] = $info->getFilename();
                    $p['size'] = $info->getSize();
                    $p['ext'] = $info->getExtension();
                    $p['md5'] = $info->hash('md5');
                    $p['sha1'] = $info->hash('sha1');
                    $p['create_time'] = time();
                    $res = Db('pictures')->insertGetId($p);
                    if ($res) {
                        return json(array('code' => 1, 'thumb' => $res, 'pic' => $new_file_path));
                    } else {
                        return json(array('code' => 0, 'msg' => '上传失败~'));
                    }
                } else {
                    return json(array('code' => 0, 'msg' => '文件格式不支持~'));
                }
            } else {
                return json(array('code' => 0, 'msg' => '上传失败~'));
            }
        }
    }

    //添加水印

    public function waterMark($file = '')
    {
        //水印图片
        $waterPic = ROOT_PATH . Db('pictures')->where(array('id' => config('system_config')['upload_thumb_water_pic']))->value('path');
        // 读取图片
        $image = Image::open($file);
        // 添加水印
        $image->water($waterPic, config('system_config')['upload_thumb_water_position'], config('system_config')['upload_thumb_water_alpha']);
        // 保存水印图片，覆盖原图
        $image->save($file);
    }


    //缩略图

    public function makeThumb($file = '')
    {
        // 获取要生成的缩略图最大宽度和高度
        list($thumb_max_width, $thumb_max_height) = explode(',', config('system_config')['upload_image_thumb']);
        // 读取图片
        $image = Image::open($file);
        // 保存缩略图
        $save_dir = ROOT_PATH . 'uploads' . DS . 'thumb/' . date('Ymd') . '/';
        $thumb_path = '/uploads/thumb/' . date('Ymd') . '/';
        $thumb_name = md5(microtime(true)) . '.' . $image->type();
        if (!is_dir($save_dir)) {
            mkdir($save_dir, 0766, true);
        }
        // 生成缩略图
        $image->thumb($thumb_max_width, $thumb_max_height, config('system_config')['upload_image_thumb_type'])->save($save_dir . $thumb_name);
        return $thumb_path . $thumb_name;
    }


    //移除文件

    public function remove()
    {
        $path = Db('pictures')->where(array('id' => input('id')))->value('path');
        if (unlink('.' . $path)) {
            $res = Db('pictures')->delete(input('id'));
            if ($res) {
                $this->success('删除成功~');
            } else {
                $this->error('删除失败~');
            }
        } else {
            $this->error('删除失败~');
        }
    }

}