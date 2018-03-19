<?php
/**
 * Created by PhpStorm.
 * User: Xiao（229622916@qq.com）
 * Date: 2018/1/19
 * Time: 13:49
 * Intro:
 */
namespace app\admin\model;

use think\Model;

class Xcms extends Model
{

    protected $table = '__FIELDS__';

    protected $autoWriteTimestamp = false;

    //获取添加表单字段、数据
    public function getAddFields($moduleid)
    {
        $fields = self::where(array('status' => 1, 'isdel' => 1, 'moduleid' => $moduleid))->order('sort asc')->select();
        foreach ($fields as $key => $f) {
            if ($f['name'] == 'cates') {
                $list[$key]['name'] = $f['name'];
                $list[$key]['title'] = $f['title'];
                $list[$key]['type'] = $f['type'];
                $list[$key]['value'] = $f['value'];
                $list[$key]['options'] = getCates();
                $list[$key]['tips'] = $f['tips'];
            } else {
                $list[$key]['name'] = $f['name'];
                $list[$key]['title'] = $f['title'];
                $list[$key]['type'] = $f['type'];
                $list[$key]['value'] = $f['value'];
                $list[$key]['options'] = $f['options'];
                $list[$key]['tips'] = $f['tips'];
            }
        }
        return $list;
    }


    //获取修改字段、数据
    public function getEditFields($moduleid, $table, $id)
    {
        if ($table) {
            $info = Db($table)->where(array('id' => $id))->find();
        }
        $fields = self::where(array('status' => 1, 'isdel' => 1, 'moduleid' => $moduleid))->order('sort asc')->select();
        foreach ($fields as $key => $f) {
            $list[$key]['name'] = $f['name'];
            $list[$key]['title'] = $f['title'];
            $list[$key]['value'] = isset($info[$f['name']]) ? $info[$f['name']] : $f['value'];
            if ($f['type'] == 'image') {
                if ($info[$f['name']]) {
                    $list[$key]['value'] = $info[$f['name']];
                    $list[$key]['thumb'] = Db('pictures')->where(array('id' => $info[$f['name']]))->value('path');
                }
            } elseif ($f['type'] == 'images') {
                if ($info[$f['name']]) {
                    $list[$key]['value'] = $info[$f['name']];
                    $list[$key]['options'] = $this->getPic($info[$f['name']]);
                }
            } else {
                $list[$key]['options'] = $f['options'];
            }
            $list[$key]['type'] = $f['type'];
            $list[$key]['tips'] = $f['tips'];
        }
        return $list;
    }


    //获取图片
    public function getPic($str)
    {
        $arr = explode(',', $str);
        $map['id'] = array('in', array_filter($arr));
        $plists = Db('pictures')->where($map)->select();
        foreach ($plists as $key => $p) {
            $list[$key]['value'] = $p['id'];
            $list[$key]['thumb'] = $p['path'];
        }
        return $list;
    }

    //获取验证规则
    public function getRules($moduleid)
    {
        $lists = self::where(array('status' => 1, 'isdel' => 1, 'moduleid' => $moduleid, 'required' => 1))->order('sort asc')->select();
        foreach ($lists as $key => $l) {
            $name = $l['name'] . '|' . $l['title'];
            $rules[$key][$name] = 'require|' . $l['rules'] . '|length:2,' . $l['length'];
        }
        return $this->merge_array($rules);
    }


    //获取验证规则msg
    public function getRulesMsg($moduleid)
    {
        $lists = self::where(array('status' => 1, 'isdel' => 1, 'moduleid' => $moduleid, 'required' => 1))->order('sort asc')->select();
        foreach ($lists as $key => $l) {
            $name = $l['name'] . '.require';
            $length = $l['name'] . '.length';
            $rules[$key][$name] = $l['title'] . '不能为空';
            $rules[$key][$length] = $l['title'] . '长度2-' . $l['length'];
        }
        return $this->merge_array($rules);
    }


    //二维数组合并
    function merge_array($array)
    {
        return call_user_func_array('array_merge', $array);
    }


}