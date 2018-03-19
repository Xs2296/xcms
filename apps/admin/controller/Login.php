<?php
/**
 * Created by PhpStorm.
 * User: Xiao（229622916@qq.com）
 * Date: 2017/11/30
 * Time: 9:51
 * Intro:
 */
namespace app\admin\controller;

use think\Controller;
use think\Request;

class Login extends Controller
{

    //后台登录
    public function index()
    {
        if (Request::instance()->isPost()) {
            $datas = input();
            $remember = isset($datas['remember']) ? true : false;
            $validateRes = $this->validate($datas, 'Login');
            if (true !== $validateRes) {
                $this->error($validateRes);
            }

            if (config('system_config')['captcha'] == 1) {
                $captcha = $this->request->post('captcha', '');
                $captcha == '' && $this->error('请输入验证码~');
                if (!captcha_check($captcha)) {
                    $this->error('验证码错误或失效~');
                };
            }

            $uuid = model('Admin')->login($datas['username'], $datas['password'], $remember);
            if ($uuid) {
                $this->success('登录成功~', url('admin/index/index'));
            } else {
                $this->error(model('Admin')->getError());
            }
        }
        if (isLogin()) {
            $this->redirect('admin/index/index');
        } else {
            $this->assign('captcha', config('system_config')['captcha']);
            return $this->fetch('public/login');
        }
    }

    //退出登录
    public function logout()
    {
        session(null);
        cookie('uuid', null);
        cookie('admin_token', null);
        $this->redirect('index');
    }
}