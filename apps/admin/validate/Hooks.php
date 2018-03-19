<?php
/**
 * Created by PhpStorm.
 * User: Xiao（229622916@qq.com）
 * Date: 2018/1/5
 * Time: 17:10
 * Intro:
 */
namespace app\admin\validate;

use think\Validate;

class Hooks extends Validate
{

    protected $rule = [
//        'name' => '/^[_0-9a-z]{6,16}$/i',   //正则表达式
        'plugin|所属插件' => 'require|alpha|length:3,10',
        'name|钩子名称' => 'require|alphaDash|length:3,10',
        'title|钩子描述' => 'require|length:2,20',
    ];

    //定义验证提示
    protected $message = [
        'plugin.require' => '请输入所属插件',
        'plugin.length' => '所属插件长度3-10位',
        'name.require' => '请输入钩子名称',
        'name.length' => '钩子名称长度3-10位',
        'title.require' => '请输入钩子描述',
        'title.length' => '钩子描述长度2-20位'
    ];
}