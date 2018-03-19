<?php
/**
 * Created by PhpStorm.
 * User: Xiao（229622916@qq.com）
 * Date: 2017/11/30
 * Time: 16:20
 * Intro:
 */
namespace app\Admin\validate;

use think\Validate;

class Login extends Validate
{

    protected $rule = [
        'username|用户名' => 'require|length:5,10',
        'password|密码' => 'require|length:6,20',
    ];

    //定义验证提示
    protected $message = [
        'username.require' => '请输入用户名',
        'username.length' => '用户名长度5-20位',
        'password.require' => '请输入密码',
        'password.length' => '密码长度6-20位',
    ];

}