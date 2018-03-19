<?php
/**
 * Created by PhpStorm.
 * User: s____
 * Date: 2018/1/13
 * Time: 13:41
 */
namespace app\admin\validate;

use think\Validate;

class Fields extends Validate
{

    protected $rule = [
        'title|标题' => 'require|chsAlphaNum|length:2,10',
        'name|字段名' => 'require|alphaDash|length:2,10',
    ];

    //定义验证提示
    protected $message = [
        'type.require' => '请选择字段类型',
        'title.require' => '请输入标题',
        'title.length' => '标题长度2-10位',
        'name.require' => '请输入字段名',
        'name.length' => '字段名长度2-10位',
    ];
}