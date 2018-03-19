<?php
/**
 * Created by PhpStorm.
 * User: Xiao（229622916@qq.com）
 * Date: 2018/1/12
 * Time: 11:09
 * Intro:
 */
namespace app\admin\validate;

use think\Validate;

class Module extends Validate
{

    protected $rule = [
        'title|模型名称' => 'require|length:2,20',
        'name|表名' => 'require|alphaDash|length:3,10',
    ];

    //定义验证提示
    protected $message = [
        'title.require' => '请输入模型名称',
        'title.length' => '模型名称长度2-20位',
        'name.require' => '请输入模型名称',
        'name.length' => '表名长度3-10位',
    ];
}