<?php
/**
 * Created by PhpStorm.
 * User: s____
 * Date: 2017/12/2
 * Time: 13:39
 */
namespace app\common\behavior;

use app\admin\model\Module;
use think\Request;
class Config
{

    public function run(&$params)
    {
        // 后台公共模板
        $BaseLayout = APP_PATH . 'admin/view/layout.html';
        $AdminHome = APP_PATH . 'admin/view/index.html';
        config('BaseLayout', $BaseLayout);
        config('AdminHome', $AdminHome);

        //默认缩略图
        $DefaultPic = '/statics/oneui/img/default.png';
        config('DefaultPic', $DefaultPic);

        //视图输出字符串内容替换
        $view_replace_str = [
            '__STATICS__' => '/statics',
            '__ONEUI__' => '/statics/oneui',
            '__UEDITOR__' => '/statics/ueditor',
            '__UPLOADS__' => '/uploads',
            '__PUBLIC__' => '/public',
        ];
        config('view_replace_str', $view_replace_str);

        //auth配置
        $auth = [
            'auth_on' => 1, // 权限开关
            'auth_type' => 1, // 认证方式，1为实时认证；2为登录认证。
            'auth_group' => 'auth_group', // 用户组数据不带前缀表名
            'auth_group_access' => 'auth_group_access', // 用户-用户组关系不带前缀表名
            'auth_rule' => 'auth_rule', // 权限规则不带前缀表名
            'auth_user' => 'admin', // 用户信息不带前缀表名
        ];
        config('auth', $auth);

        //分页配置
        $paginate = [
            'type' => 'bootstrap',
            'var_page' => 'page',
            'list_rows' => 15,
            'query' => request()->param(),
        ];
        config('paginate', $paginate);

        //系统配置
        $ConfigModel = new \app\admin\model\Config();
        $system_config = $ConfigModel->getConfig();
        // 非开发模式，缓存系统配置
        if ($system_config['develop'] == 0) {
            cache('system_config', $system_config);
        }
        config('system_config',$system_config);

        //模型配置
        $ModuleModel = new Module();
        $modules = $ModuleModel->where(array('status'=>1,'isdel'=>1))->select();
        config('modules',$modules);
    }
}