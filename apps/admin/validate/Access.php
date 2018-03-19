<?php
/**
 * Created by PhpStorm.
 * User: Xiao（229622916@qq.com）
 * Date: 2018/2/5
 * Time: 15:26
 * Intro:
 */
namespace app\admin\validate;

use think\Validate;

class Access extends Validate
{

    protected $rule = [
        'title|用户组名' => 'require|chsDash|length:2,20',
    ];

    //定义验证提示
    protected $message = [
        'title.require' => '请输入用户组名',
        'title.length' => '用户组名长度2-20位'
    ];

}