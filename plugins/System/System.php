<?php
/**
 * Created by PhpStorm.
 * User: s____
 * Date: 2017/12/2
 * Time: 15:35
 */
namespace plugins\System;
use app\common\controller\Plugin;
use think\Model;
class System extends Plugin{

    public function dev()
    {
        $version = Db()->query('SELECT VERSION() AS ver');
        $config  = [
            'url'             => $_SERVER['HTTP_HOST'],
            'think_version' =>  THINK_VERSION,
            'php_version'   =>  PHP_VERSION,
            'mysql_version' =>  $version[0]['ver'],
            'server_os' =>  PHP_OS,
            'server_ip'       => $_SERVER['SERVER_ADDR'],
            'max_upload_size'    =>  ini_get('upload_max_filesize'),
            'server'    =>  $_SERVER['SERVER_SOFTWARE']
        ];
        $this->assign('sys',$config);
        $this->fetch('index');
    }


}