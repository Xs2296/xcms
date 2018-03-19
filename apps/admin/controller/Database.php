<?php
/**
 * Created by PhpStorm.
 * User: Xiao（229622916@qq.com）
 * Date: 2017/12/26
 * Time: 9:54
 * Intro:数据库备份、还原
 */
namespace app\admin\controller;

use think\Db;
use app\common\builder\Xbuilder;

class Database extends Base
{

    public function index($group = 'export')
    {
        switch ($group) {
            case 'export':
                $tables = Db::query("SHOW TABLE STATUS");
                $data_list = array_map('array_change_key_case', $tables);
                return Xbuilder::make('table')
                    ->setTitle('备份数据库')
                    ->setButtons('export,optimize,repair')
                    ->setColumns(['name|表名', 'rows|行数', 'data_length|大小', 'data_free|冗余', 'comment|备注'])
                    ->setOps('optimize,repair')
                    ->setDatas($data_list)
                    ->fetch();
                break;
            case 'import':
                $path = ROOT_PATH . config('system_config')['data_path'];
                if (!is_dir($path)) {
                    mkdir($path, 0755, true);
                }
                $path = realpath($path);
                $flag = \FilesystemIterator::KEY_AS_FILENAME;
                $glob = new \FilesystemIterator($path, $flag);
                $data_list = [];
                foreach ($glob as $name => $file) {
                    if (preg_match('/^[\s\S]*?@\d{8,8}-\d{10,10}-\d+\.sql(?:\.gz)?$/', $name)) {
                        $str = explode('@', $name);
                        $name = sscanf($name, '%' . strlen($str[0]) . 's@%4s%2s%2s-%10s-%d');
                        $date = "{$name[1]}-{$name[2]}-{$name[3]}";
                        $time = "{$name[4]}";
                        $part = $name[5];
                        if (isset($data_list["{$date} {$time}"])) {
                            $info = $data_list["{$date} {$time}"];
                            $info['part'] = max($info['part'], $part);
                            $info['size'] = $info['size'] + $file->getSize();
                        } else {
                            $info['part'] = $part;
                            $info['size'] = $file->getSize();
                        }
                        $extension = strtoupper(pathinfo($file->getFilename(), PATHINFO_EXTENSION));
                        $info['compress'] = ($extension === 'SQL') ? '-' : $extension;
                        $info['title'] = $name[0] . '@' . $name[1] . $name[2] . $name[3] . '-' . $name[4] . '-' . $name[5];
                        $info['times'] = date('Y-m-d H:i:s', $time);
                        $info['name'] = $time;
                        $data_list["{$date} {$time}"] = $info;
                    }
                }
                $data_list = !empty($data_list) ? array_values($data_list) : $data_list;
                return Xbuilder::make('table')
                    ->setTitle('还原数据库')
                    ->setColumns(['title|文件名', 'part|卷数', 'compress|压缩类型', 'size|数据大小', 'times|备份时间'])
                    ->setOps('restore,del')
                    ->setDatas($data_list)
                    ->fetch();
                break;
        }
    }


    //导出数据库
    public function export($id = '', $start = 0)
    {
        $tables = $id;
        if ($this->request->isPost() && !empty($tables) && is_array($tables)) {
            //处理数据库、数据表名
            $tablesCount = Db::getTables();
            if (count($tablesCount) == count($tables)) {
                $str = config('database.database');
            } else {
                $str = '';
                foreach ($tables as $t) {
                    $str .= str_replace(config('database.prefix'), "", $t) . '#';
                }
                $str = rtrim($str, '#');
            }
            //创建目录
            $path = ROOT_PATH . config('system_config')['data_path'];
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }
            // 压缩配置
            $config = array(
                'path' => realpath($path) . DIRECTORY_SEPARATOR,
                'part' => config('system_config')['data_part_size'],//20971520,  //备份卷大小（单位B；建议设置20M）
                'compress' => config('system_config')['data_compress'],    //压缩（0否、1是）
                'level' => config('system_config')['data_compress_level'], //备份文件压缩级别（1最低、4一般、9最高）
            );
            // 检查是否有正在执行的任务
            $lock = ROOT_PATH . config('system_config')['data_path'] . "backup.lock";
            if (is_file($lock)) {
                $this->error('检测到有一个备份任务正在执行，请稍后再试~');
            } else {
                // 创建锁文件
                file_put_contents($lock, time());
            }
            // 检查备份目录是否可写
            is_writeable($path) || $this->error('备份目录不存在或不可写，请检查后重试~');
            // 生成备份文件信息
            $file = array(
                'name' => $str . '@' . date('Ymd') . '-' . time(),
                'part' => 1,
            );
            // 创建备份文件
            $Database = new \app\admin\model\Database($file, $config);
            if (false !== $Database->createSql()) {
                // 备份指定表
                foreach ($tables as $t) {
                    $start = $Database->backup($t, $start);
                    while (0 !== $start) {
                        if (false === $start) { // 出错
                            $this->error('备份出错~');
                        }
                        $start = $Database->backup($t, $start[0]);
                    }
                }
                // 备份完成，删除锁定文件
                unlink($lock);
                $this->success('备份完成~');
            } else {
                $this->error('初始化失败，备份文件创建失败~');
            }
        }

    }


    //导入数据库
    public function import($time = '')
    {
        if ($time == 0) $this->error('参数错误~');
        // 初始化
        $name = '*@' . date('Ymd', $time) . '-' . $time . '-*.sql*';
        $root = ROOT_PATH . config('system_config')['data_path'];
        $path = realpath($root) . DIRECTORY_SEPARATOR . $name;
        $files = glob($path);
        $list = array();
        foreach ($files as $name) {
            $basename = basename($name);
            $str = explode('@', $basename);
            $match = sscanf($basename, '%' . strlen($str[0]) . 's@%4s%2s%2s-%10s-%d');
            $gz = preg_match('/^[\s\S]*?@\d{8,8}-\d{10,10}-\d+\.sql.gz$/', $basename);
            $list[$match[5]] = array($match[5], $name, $gz);
        }
        ksort($list);
        // 检测文件正确性
        $last = end($list);
        if (count($list) == $last[0]) {
            foreach ($list as $item) {
                $path = ROOT_PATH . config('system_config')['data_path'];
                $config = [
                    'path' => realpath($path) . DIRECTORY_SEPARATOR,
                    'compress' => $item[2]
                ];
                $Database = new \app\admin\model\Database($item, $config);
                $start = $Database->import(0);
                // 循环导入数据
                while (0 !== $start) {
                    if (false === $start) { // 出错
                        $this->error('还原数据出错~');
                    }
                    $start = $Database->import($start[0]);
                }
            }
            $this->success('还原完成~');
        } else {
            $this->error('备份文件可能已经损坏，请检查！');
        }
    }


    //优化表
    public function optimize($id = '')
    {
        $tables = $id;
        if ($tables) {
            if (is_array($tables)) {
                $tables = implode('`,`', $tables);
                $list = Db::query("OPTIMIZE TABLE `{$tables}`");
                if ($list) {
                    $this->success("数据表优化完成~");
                } else {
                    $this->error("数据表优化出错~");
                }
            } else {
                $list = Db::query("OPTIMIZE TABLE `{$tables}`");
                if ($list) {
                    $this->success("数据表'{$tables}'优化完成~");
                } else {
                    $this->error("数据表'{$tables}'优化出错~");
                }
            }
        } else {
            $this->error("请选择要优化的表~");
        }
    }


    //修复表
    public function repair($id = '')
    {
        $tables = $id;
        if ($tables) {
            if (is_array($tables)) {
                $tables = implode('`,`', $tables);
                $list = Db::query("REPAIR TABLE `{$tables}`");
                if ($list) {
                    $this->success("数据表修复完成~");
                } else {
                    $this->error("数据表修复出错~");
                }
            } else {
                $list = Db::query("REPAIR TABLE `{$tables}`");
                if ($list) {
                    $this->success("数据表'{$tables}'修复完成~");
                } else {
                    $this->error("数据表'{$tables}'修复出错~");
                }
            }
        } else {
            $this->error("请指定要修复的表~");
        }
    }

    //删除备份
    public function delete($id = '')
    {
        $time = $id;
        $name = '*@' . date('Ymd', $time) . '-' . $time . '-*.sql*';
        $root = ROOT_PATH . config('system_config')['data_path'];
        $path = realpath($root) . DIRECTORY_SEPARATOR . $name;
        $files = glob($path);
        if (!unlink($files[0])) {
            $this->error('操作失败~');
        } else {
            $this->success('操作成功~');
        }
    }
}