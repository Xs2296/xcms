<?php
/**
 * Created by PhpStorm.
 * User: Xiao（229622916@qq.com）
 * Date: 2018/1/3
 * Time: 17:22
 * Intro:
 */
namespace app\admin\model;

use think\Model;

class Config extends Model
{

    protected $table = '__CONFIG__';

    protected $autoWriteTimestamp = true;

    public function getConfig($name = '')
    {
        $configs = self::column('value,type', 'name');
        $result = [];
        foreach ($configs as $config) {
            switch ($config['type']) {
                case 'textarea':
                    $result[$config['name']] = parse_attr($config['value']);
                    break;
                case 'checkbox':
                    if ($config['value'] != '') {
                        $result[$config['name']] = explode(',', $config['value']);
                    } else {
                        $result[$config['name']] = [];
                    }
                    break;
                default:
                    $result[$config['name']] = $config['value'];
                    break;
            }
        }
        return $name != '' ? $result[$name] : $result;
    }

}