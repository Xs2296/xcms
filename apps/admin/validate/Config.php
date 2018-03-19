<?php
/**
 * Created by PhpStorm.
 * User: Xiao（229622916@qq.com）
 * Date: 2018/1/5
 * Time: 9:51
 * Intro:
 */
namespace app\admin\validate;

use think\Validate;

class Config extends Validate
{

    protected $rule = [
        'name|配置名称' => 'require|alphaDash|length:3,20',
        'title|配置标题' => 'require|chs|length:2,20',
    ];

    //定义验证提示
    protected $message = [
        'group.require' => '请选择配置分组',
        'type.require' => '请选择配置类型',
        'name.require' => '请输入配置名称',
        'name.length' => '配置名称长度3-20位',
        'title.require' => '请输入配置标题',
        'title.length' => '配置标题长度2-20位'
    ];

}