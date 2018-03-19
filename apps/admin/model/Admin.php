<?php
/**
 * Created by PhpStorm.
 * User: Xiao（229622916@qq.com）
 * Date: 2017/11/30
 * Time: 19:13
 * Intro:
 */
namespace app\admin\model;

use think\Model;
use think\Db;
use think\Session;

class Admin extends Model
{

    protected $table = '__ADMIN__';

    protected $autoWriteTimestamp = false;

    protected $pk = 'uuid';

    //检验密码
    public static function yzPassword($admin, $postPass)
    {
        $password = Md5($postPass . $admin['salt']);
        if ($password != $admin['password']) {
            return false;
        }
        return true;
    }

    //用户登录
    public function login($username = '', $password = '', $remember = false)
    {
        $count = self::where(['name' => $username, 'isdel' => 1])->count();
        if ($count == 0) {
            $this->error = '用户不存在~';
        } else {
            $admin = self::get(['name' => $username])->toArray();
            if ($admin['status' == 2]) {
                $this->error = '用户被禁用~';
                return false;
            }
            if (!self::yzPassword($admin, $password)) {
                $this->error = '密码错误~';
                return false;
            }
            if (self::where('name', $username)->update(['last_login_time' => time()])) {
                return self::autoLogin($admin['uuid'], $remember);
            } else {
                $this->error = '登录异常，请重新登录~';
                return false;
            }
        }
        return false;
    }

    //自动登录
    public function autoLogin($uuid, $rememberme = false)
    {
        $admin = self::get($uuid);
        $sessionAuth = array(
            'uuid' => $admin['uuid'],
            'salt' => $admin['salt'],
            'last_login_time' => $admin['last_login_time'],
        );
        session('admin_auth', $sessionAuth);
        session('admin_auth_sign', dataSign($sessionAuth));
        if ($rememberme) {
            $cookieAuth = $admin['uuid'] . $admin['name'] . $admin['last_login_time'];
            cookie('uuid', $admin['uuid'], 24 * 3600 * 7);
            cookie('admin_token', dataSign($cookieAuth), 24 * 3600 * 7);
        }
        return $admin['uuid'];
    }

}