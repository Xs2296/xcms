<?php
/**
 * Created by PhpStorm.
 * User: Xiao（229622916@qq.com）
 * Date: 2018/1/4
 * Time: 11:00
 * Intro:
 */
namespace app\admin\controller;

use app\admin\model\Config as ConfigModel;
use app\common\builder\Xbuilder;

class System extends Base
{

    public function index($group = 'system')
    {
        if ($this->request->isPost()) {
            $data = input();
            $items = ConfigModel::where('group', $group)->where(array('status' => 1, 'isdel' => 1))->column('name,type');
            foreach ($items as $name => $type) {
                if (!isset($data[$name])) {
                    switch ($type) {
                        case 'switch':
                            $data[$name] = 0;
                            break;
                        case 'checkbox':
                            $data[$name] = '';
                            break;
                    }
                } else {
                    if (is_array($data[$name])) {
                        $data[$name] = implode(',', $data[$name]);
                    }
                    switch ($type) {
                        case 'switch':
                            $data[$name] = 1;
                            break;
                    }
                }
                ConfigModel::where('name', $name)->update(['value' => $data[$name]]);
            }
            cache('system_config', null);
            $this->success('更新成功~', url('index', ['group' => $group]));
        }

        // 配置分组信息
        $list_group = config('system_config')['groups'];
        $tab_list = [];
        foreach ($list_group as $key => $value) {
            $tab_list[$key]['title'] = $value;
            $tab_list[$key]['url'] = url('index', ['group' => $key]);
            $tab_list[$key]['value'] = $key;
        }
        $this->assign('tabs', $tab_list);
        //页面渲染
        $data_list = ConfigModel::where(array('group' => $group, 'status' => 1, 'isdel' => 1))
            ->order('sort desc,id asc')
            ->column('*');
        // 解析options
        foreach ($data_list as &$value) {
            if ($value['options'] != '') {
                $value['options'] = parse_attr($value['options']);
            }
            //水印图片处理
            if ($value['name'] == 'upload_thumb_water_pic') {
                $value['thumb'] = Db('pictures')->where(array('id' => $value['value']))->value('path');
            }
        }
        return Xbuilder::make('form')
            ->setTitle('系统设置')
            ->setFormData($data_list)
            ->fetch();
    }
}