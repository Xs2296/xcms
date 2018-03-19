<?php
/**
 * Created by PhpStorm.
 * User: Xiao（229622916@qq.com）
 * Date: 2018/1/19
 * Time: 14:08
 * Intro:
 */
namespace app\admin\validate;

use think\Validate;

class Xcms extends Validate
{


    protected $rule;

    protected $message;

    public function __construct()
    {
        $XcmsModel = new \app\admin\model\Xcms();
        $this->rule = $XcmsModel->getRules(input('moduleid'));
        $this->message = $XcmsModel->getRulesMsg(input('moduleid'));
    }


}