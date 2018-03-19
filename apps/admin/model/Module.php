<?php
/**
 * Created by PhpStorm.
 * User: Xiao（229622916@qq.com）
 * Date: 2018/1/12
 * Time: 10:30
 * Intro:模型类模型
 */
namespace app\admin\model;

use think\Model;
use think\Db;

class Module extends Model
{

    protected $table = '__MODULE__';

    protected $autoWriteTimestamp = true;

    public static function makeTable($moduleid, $title, $table, $type)
    {
        if ($type == 1) {
            Db::execute("
                CREATE TABLE `" . config('database.prefix') . $table . "` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `status` tinyint(2) DEFAULT '1' COMMENT '0禁用、1启用',
                  `create_time` int(11) DEFAULT NULL,
                  `update_time` int(11) DEFAULT NULL,
                  `isdel` tinyint(2) DEFAULT '1' COMMENT '0已删除、1正常',
                PRIMARY KEY (`id`),
                  KEY `{$table}_id` (`id`)
                ) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COMMENT='$title';
            ");
        } else {
            Db::execute("
                CREATE TABLE `" . config('database.prefix') . $table . "` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `cates` int(11) DEFAULT '0',
                  `title` varchar(225) DEFAULT NULL,
                  `litpic` varchar(225) DEFAULT NULL,
                  `content` mediumtext DEFAULT NULL,
                  `status` tinyint(2) DEFAULT '1' COMMENT '0禁用、1启用',
                  `create_time` int(11) DEFAULT NULL,
                  `update_time` int(11) DEFAULT NULL,
                  `isdel` tinyint(2) DEFAULT '1' COMMENT '0已删除、1正常',
                PRIMARY KEY (`id`),
                  KEY `{$table}_id` (`id`),
                  KEY `{$table}_cates` (`cates`),
                  KEY `{$table}_id_cates` (`id`,`cates`)
                ) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COMMENT='$title';
            ");
            Db::execute("INSERT INTO `" . config('database.prefix') . "fields` VALUES ('', '{$moduleid}', '栏目', 'cates', '', '1', 'number', 'select', '', '', '100', '1', '1', '1')");
            Db::execute("INSERT INTO `" . config('database.prefix') . "fields` VALUES ('', '{$moduleid}', '标题', 'title', '', '1', 'chsDash', 'text', '', '', '100', '2', '1', '1')");
            Db::execute("INSERT INTO `" . config('database.prefix') . "fields` VALUES ('', '{$moduleid}', '缩略图', 'litpic', '', '0', '', 'text', '', '', '100', '3', '1', '1')");
            Db::execute("INSERT INTO `" . config('database.prefix') . "fields` VALUES ('', '{$moduleid}', '内容', 'content', '', '0', '', 'ueditor', '', '', '255', '4', '1', '1')");
        }
    }


}