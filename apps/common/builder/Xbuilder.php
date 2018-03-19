<?php
/**
 * Created by PhpStorm.
 * User: Xiao（229622916@qq.com）
 * Date: 2017/11/23
 * Time: 16:02
 * Intro:
 */
namespace app\common\builder;

use think\Controller;

class Xbuilder extends Controller
{

    //模板参数
    protected static $vars = [];

    //构建器方法
    public static function make($type = '')
    {
        if ($type == '') {
            throw new Exception('未指定构建器名称', 8001);
        } else {
            $type = strtolower($type);
        }

        // 构造器类路径
        $class = '\\app\\common\\builder\\' . $type . '\\Builder';
        if (!class_exists($class)) {
            throw new Exception($type . '构建器不存在', 8002);
        }
        return new $class;
    }


    //模板输出
    public function fetch($template = '', $vars = [], $replace = [], $config = [])
    {
        $vars = array_merge($vars, self::$vars);
        return parent::fetch($template, $vars, $replace, $config);
    }


}