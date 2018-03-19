<?php
/**
 * Created by PhpStorm.
 * User: Xiao（229622916@qq.com）
 * Date: 2018/1/26
 * Time: 13:56
 * Intro:
 */
namespace app\admin\validate;

use think\Validate;

class Category extends Validate
{

    protected $rule = [
        'moduleid|所属模型' => 'require|number|length:1,20',
        'catname|栏目名称' => 'require|chsDash|length:2,20',
    ];

    //定义验证提示
    protected $message = [
        'moduleid.require' => '请选择所属模型',
        'catname.require' => '请输入栏目名称',
        'catname.length' => '栏目名称长度2-20位'
    ];


}