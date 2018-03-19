<?php
/**
 * Created by PhpStorm.
 * User: s____
 * Date: 2017/12/2
 * Time: 13:39
 */
namespace app\common\behavior;
class Hook
{

    //自动注册钩子
    public function run(&$params)
    {
        $hooks = cache('hooks');
        if (!$hooks) {
            $hooks = Db('Hooks')->where(array('status'=>1,'isdel'=>1))->select();
            cache('hooks', $hooks);
        }
        if ($hooks) {
            foreach ( $hooks as $h) {
                include_once 'plugins/' . $h["plugin"] . '/' . $h["plugin"] . '.php';
                \think\Hook::add($h['name'], 'plugins\\' . $h["plugin"] . '\\' . $h["plugin"]);
            }
        }
    }

}