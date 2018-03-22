<?php
/**
 * Created by PhpStorm.
 * User: Xiao（229622916@qq.com）
 * Date: 2017/11/24
 * Time: 15:59
 * Intro:
 */
namespace app\admin\controller;

use app\admin\model\Admin as AdminModel;
use think\Cache;

class Index extends Base
{

    //后台首页
    public function index()
    {
        return $this->fetch(config('AdminHome'));
    }


    //清除缓存
    public function clearCache()
    {
        $cache = config('system_config')['cache'];
        if (!empty($cache)) {
            foreach ($cache as $c) {
                if ($c == 'LOG_PATH') {
                    $dirs = (array)glob(constant($c) . '*');
                    foreach ($dirs as $dir) {
                        array_map('unlink', glob($dir . '/*.log'));
                    }
                    array_map('rmdir', $dirs);
                } else {
                    array_map('unlink', glob(constant($c) . '/*.*'));
                }
            }
            Cache::clear();
            $this->success('清除成功~', url('admin/index/index'));
        } else {
            $this->error('清除失败~');
        }
    }

    //锁定屏幕
    public function lock()
    {
        session('lock', 1);
        $this->success('锁屏成功~', url('admin/index/unlock'));
    }

    //解屏
    public function unlock()
    {
        if ($this->request->isPost()) {
            $info = AdminModel::where(array('uuid' => isLogin()))->find();
            $res = AdminModel::yzPassword($info, input('password'));
            if ($res) {
                session('lock', null);
                $this->success('解屏成功~', url('admin/index/index'));
            } else {
                $this->error('密码错误~');
            }
        }
        return $this->fetch('public/unlock');
    }

}