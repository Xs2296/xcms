<?php
/**
 * Created by PhpStorm.
 * User: s____
 * Date: 2017/12/2
 * Time: 15:35
 */
namespace app\common\controller;

use think\View;
use think\Exception;

abstract class Plugin
{

    protected $view = '';

    protected $path = '';

    public function __construct()
    {
        $this->view = new View();
        $this->path = 'plugins/' . $this->getName() . '/';
    }

    final public function getName()
    {
        $class = get_class($this);
        return substr($class, strrpos($class, '\\') + 1);
    }

    final protected function fetch($template = '', $vars = [], $replace = [], $config = [], $renderContent = false)
    {
        if ($template != '') {
            if (!is_file($template)) {
                $template = $this->path . 'view/' . $template . '.html';
                if (!is_file($template)) {
                    throw new Exception('模板不存在：' . $template, 5001);
                }
            }
            echo $this->view->fetch($template, $vars, $replace, $config, $renderContent);
        }
    }

    final protected function assign($name = '', $value = '')
    {
        $this->view->assign($name, $value);
        return $this;
    }

}
