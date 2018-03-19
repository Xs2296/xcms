<?php
/**
 * Created by PhpStorm.
 * User: Xiao（229622916@qq.com）
 * Date: 2018/1/12
 * Time: 15:16
 * Intro:字段模型
 */
namespace app\admin\model;

use think\Model;

class Fields extends Model
{

    protected $table = '__FIELDS__';

    protected $autoWriteTimestamp = false;

    /*
     * 构建sql语句
     * $table 表名、$do 添加、修改、更新、$field 字段名、$oldfield旧字段、$type类型、$title字段描述、$default默认值、$length长度
     */
    public static function getFieldSql($table, $do, $field, $oldfield = '', $type, $title, $default, $length)
    {
        if ($do == 'add') {
            $ac = " ADD `" . $field . "` ";
        } elseif ($do == 'edit') {
            $ac = " CHANGE `" . $oldfield . "` `" . $field . "` ";
        } else {
            $ac = " modify column `" . $field . "` ";
        }
        switch ($type) {
            case 'date':
                if (empty($default)) {
                    $default = 0;
                }
                $sql = "ALTER TABLE `$table` $ac INT(11) DEFAULT '$default' COMMENT '$title'";
                break;
            case 'switch':
                if (empty($default)) {
                    $default = 0;
                }
                $sql = "ALTER TABLE `$table` $ac TINYINT(2) DEFAULT '$default' COMMENT '$title'";
                break;
            case 'radio':
                if (empty($default)) {
                    $default = 0;
                }
                $sql = "ALTER TABLE `$table` $ac TINYINT(2) DEFAULT '$default' COMMENT '$title'";
                break;
            case 'ueditor':
                $sql = "ALTER TABLE `$table` $ac TEXT(0) DEFAULT '$default' COMMENT '$title'";
                break;
            default:
                if ($length > 255) {
                    $sql = "ALTER TABLE `$table` $ac TEXT(0) DEFAULT '$default' COMMENT '$title'";
                } else {
                    $sql = "ALTER TABLE `$table` $ac VARCHAR($length) DEFAULT '$default' COMMENT '$title'";
                }
        }
        return $sql;
    }

    //删除字段
    public static function delFieldSql($table, $name)
    {
        $sql = " ALTER  TABLE `" . config('database.prefix') . $table . "` drop column `" . $name . "` ";
        return $sql;
    }
}