<?php
/**
 * Created by PhpStorm.
 * User: Xiao（229622916@qq.com）
 * Date: 2018/2/6
 * Time: 11:43
 * Intro:
 */
namespace app\admin\validate;

use think\Validate;

class User extends Validate
{
    protected $rule;
    protected $message;
    protected $rule1 = [
        'group|用户组' => 'require|number|length:1,20',
        'name|用户名' => 'require|alphaNum|length:2,20',
        'password|密码' => 'require|alphaNum|length:2,20',
    ];

    //定义验证提示
    protected $message1 = [
        'group.require' => '请选择用户组',
        'name.require' => '请输入用户名',
        'name.length' => '用户名长度2-20位',
        'password.require' => '请输入密码',
        'password.length' => '密码长度2-20位'
    ];

    public function __construct()
    {
        if (input('type') == 1) {
            $this->rule = $this->rule1;
            $this->message = $this->message1;
        }
    }


}