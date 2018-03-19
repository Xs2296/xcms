<?php
/**
 * Created by PhpStorm.
 * User: Xiao（229622916@qq.com）
 * Date: 2017/11/24
 * Time: 15:59
 * Intro: 基类(面包屑、权限)
 */
namespace app\admin\controller;

use app\admin\model\Access as AccessModel;
use think\Controller;

class Base extends Controller
{

    public function __construct()
    {
        parent::__construct();
        // 判断是否登录，并定义用户ID常量
        defined('uuid') or define('uuid', $this->isLogin());
        $this->setDatas();
        $this->isLock();
        $this->isAuth();
    }


    //判断是否登录
    public function isLogin()
    {
        if ($uuid = isLogin()) {
            return $uuid;
        } else {
            $this->redirect('admin/Login/index');
        }
    }


    //模版数据渲染
    public function setDatas()
    {
        //模版渲染
        $this->assign('BaseLayout', Config('BaseLayout'));
        $this->assign('DefaultPic', Config('DefaultPic'));
        $this->assign('themes', config('system_config')['themes']);
        //访问模块
        $this->assign('ac', request()->controller());
        $this->assign('group', input('group'));
        $this->assign('modules', Config('modules'));
        $this->assign('uuid', isLogin());
    }

    //判断是否锁屏
    public function isLock()
    {
        if (session('?lock') && request()->action() != 'unlock') {
            $this->redirect('admin/index/unlock');
        }
    }


    //判断是否授权
    public function isAuth()
    {
        $uuid = isLogin();
        $url = 'admin/' . request()->controller() . '/' . request()->action();
        $authRes = AccessModel::checkAuthRule($uuid, $url);
        if (!$authRes) {
            $this->error('未授权~');
        }
    }

}