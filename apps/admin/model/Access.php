<?php
/**
 * Created by PhpStorm.
 * User: s____
 * Date: 2017/12/2
 * Time: 22:29
 */
namespace app\admin\model;

use think\Model;
use think\Request;

class Access extends Model
{

    protected $table = '__AUTH_GROUP__';
    protected $autoWriteTimestamp = false;

    //生成节点
    public static function makeRule()
    {
        $module = Request::instance()->module();
        $handler = opendir(APP_PATH . '/' . $module . '/controller');
        while (($filename = readdir($handler)) !== false) {
            if ($filename != '' && $filename != '.' && $filename != '..') {
                $info = explode('.', $filename);
                if ($info[0] != 'Base' && $info[0] != 'Test') {   //过滤控制器
                    $files[] = $info[0];
                }
            }
        }
        foreach ($files as $key => $file) {
            $content = file_get_contents(APP_PATH . '/' . $module . '/controller/' . $file . '.php');
            preg_match_all("/.*?public.*?function(.*?)\(.*?\)/i", $content, $matches);
            $functions = $matches[1];
            $list[$key]['name'] = $file;
            foreach ($functions as $k => $func) {
                $func = trim($func);
                if (!in_array($func, ['_initialize', '__construct', 'test'])) {
                    $list[$key]['list'][] = $func;
                }
            }
        }
        return $list;
    }


    //校验节点是否存在
    public static function yzFunction($controller, $action, $id)
    {
        $class = "app\\admin\\controller\\{$controller}";
        if (!class_exists($class)) {
            Db('auth_rule')->where(array('id' => $id))->delete();
        } else {
            $controller = new  $class;
            if (!method_exists($controller, $action)) {
                Db('auth_rule')->where(array('id' => $id))->delete();
            }
        }
    }

    //获取用户组名称
    public function getGroupName($uuid)
    {
        $group_id = Db('auth_group_access')->where(array('uuid' => $uuid))->value('group_id');
        return self::where(array('id' => $group_id))->value('title');
    }

    //获取用户授权节点
    public static function getAuthRule($uuid = 0)
    {
        $group_id = Db('auth_group_access')->where(['uuid' => $uuid])->value('group_id');
        if ($group_id == 1) {
            return 0;
        } else {
            return self::where(array('id' => $group_id, 'status' => 1))->column('rules');
        }
    }

    //检查节点是否授权
    public static function checkAuthRule($uuid = 0, $rule = 0)
    {
        $userRules = self::getAuthRule($uuid);
        $rule_id = Db('auth_rule')->where(array('name' => $rule))->value('id');
        if ($userRules == 0) {
            return true;
        }
        $userRules = explode(',', $userRules[0]);
        if (in_array($rule_id, $userRules)) {
            return true;
        } else {
            return false;
        }
    }

}