<?php
/**
 * Created by PhpStorm.
 * User: Xiao（229622916@qq.com）
 * Date: 2018/1/20
 * Time: 15:40
 * Intro:
 */
namespace app\admin\model;

use think\Model;
use app\admin\model\Module as ModuleModel;

class Category extends Model
{

    protected static $config = [
        'id' => 'id',    // id名称
        'pid' => 'pid',   // pid名称
        'child' => 'nodes', // 子元素键名
        'level' => 'level', // 层级
        'display' => 'display', // 间隔
        'nbsp' => "&emsp;", // 间隔符
        'html' => '├─ ',   // 层级标记
    ];
    protected $table = '__CATEGORY__';
    protected $autoWriteTimestamp = true;

    public function __construct($config = [])
    {
        self::$config = array_merge(self::$config, $config);
    }

    //层次结构
    static public function toTree(&$data, $pid = 0, $count = 1)
    {
        $trees = [];
        $data = array_values($data);
        foreach ($data as $key => $value) {
            if ($value[self::$config['pid']] == $pid) {
                $value[self::$config['level']] = $count;
                $value['tags'] = [self::getArticleCount($data, $value[self::$config['id']])];
                $value[self::$config['display']] = str_repeat(self::$config['nbsp'], $count);
//                unset($data[$key]);
                $child = self::toTree($data, $value[self::$config['id']], $count + 1);
                if (!empty($child)) {
                    $value[self::$config['child']] = $child;
                }
                $trees[] = $value;
            }
        }
        return $trees;
    }


    //列表结构
    public static function toList($lists = [], $pid = 0, $level = 0)
    {
        $trees = [];
        foreach ($lists as $key => $value) {
            if ($value[self::$config['pid']] == $pid) {
                $title_prefix = str_repeat(self::$config['nbsp'], $level * 2) . self::$config['html'];
                $value['level'] = $level + 1;
                $value['prefix'] = $level == 0 ? '' : $title_prefix;
                $value['display'] = $level == 0 ? $value[self::$config['name']] : $title_prefix . $value[self::$config['name']];
                $value['childs'] = self::getChildsCount($value[self::$config['id']]);
                $trees[] = $value;
                unset($lists[$key]);
                $trees = array_merge($trees, self::toList($lists, $value[self::$config['id']], $level + 1));

            }
        }
        return $trees;
    }


    //获取文章数量
    public function getArticleCount($lists = [], $id = '')
    {
        $moduleid = self::where(array('id' => $id))->value('moduleid');
        $table = Db('module')->where(array('id' => $moduleid))->value('name');
        $ids = self::getChildsId($lists, $id);
        if ($ids) {
            $map['cates'] = array('in', $ids);
        } else {
            $map['cates'] = $id;
        }
        $map['status'] = 1;
        $map['isdel'] = 1;
        return Db($table)->where($map)->count();
    }


    //获取子栏目数量
    public static function getChildsCount($id = '')
    {
        return self::where(array('pid' => $id))->count();
    }


    //获取栏目所属模型
    public static function getCateModuleId($id)
    {
        return self::where(array('id' => $id))->value('moduleid');
    }


    //根据子节点返回所有父节点
    public static function getParents($lists = [], $id = '')
    {
        $trees = [];
        foreach ($lists as $value) {
            if ($value[self::$config['id']] == $id) {
                $trees[] = $value;
                $trees = array_merge(self::getParents($lists, $value[self::$config['pid']]), $trees);
            }
        }
        return $trees;
    }


    //获取所有子节点id
    public static function getChildsId($lists = [], $pid = '')
    {
        $result = [];
        foreach ($lists as $value) {
            if ($value[self::$config['pid']] == $pid) {
                $result[] = $value[self::$config['id']];
                $result = array_merge($result, self::getChildsId($lists, $value[self::$config['id']]));
            }
        }
        return $result;
    }


    //获取所有子节点
    public static function getChilds($lists = [], $pid = '')
    {
        $result = [];
        foreach ($lists as $value) {
            if ($value[self::$config['pid']] == $pid) {
                $result[] = $value;
                $result = array_merge($result, self::getChilds($lists, $value[self::$config['id']]));
            }
        }
        return $result;
    }


}