<?php
/**
 * Created by PhpStorm.
 * User: Xiao（229622916@qq.com）
 * Date: 2017/11/23
 * Time: 16:04
 * Intro:
 */
namespace app\common\builder\table;

use app\common\builder\Xbuilder;

class Builder extends Xbuilder
{

    private static $templates;

    private $params = [
        'title' => '',
        'buttons' => [],    //按钮
        'search' => [],     //搜索框
        'columns' => [],    //表头
        'datas' => [],  //数据列表
        'filters' => [],  //数据列表
        'pages' => '',    // 分页
        'ops' => [],    //操作
    ];


    public function _initialize()
    {
        $this->templates = APP_PATH . 'common/builder/table/layout.html';
    }

    public function setTitle($title)
    {
        if (!empty($title)) {
            $this->params['title'] = $title;
        }
        return $this;
    }

    public function setButtons($buttons = [])
    {
        if (!empty($buttons)) {
            $buttons = is_array($buttons) ? $buttons : explode(',', $buttons);
            $this->params['buttons'] = $buttons;
        }
        return $this;
    }

    public function setSearch($search)
    {
        if (!empty($search)) {
            $this->params['search'] = $search;
        }
        return $this;
    }

    public function setColumns($columns = [])
    {
        if (!empty($columns)) {
            foreach ($columns as $key => $c) {
                $row = explode('|', $c);
                $this->params['columns'][$key]['id'] = $row[0];
                $this->params['columns'][$key]['name'] = $row[1];
            }
        }
        return $this;
    }

    public function setDatas($datas = [])
    {
        if (!empty($datas)) {
            $this->params['datas'] = $datas;
        }
        return $this;
    }

    public function setFilters($filters)
    {
        if (!empty($filters)) {
            foreach ($filters as $key => $f) {
                $list[$key]['name'] = $f[0];
                $list[$key]['value'] = $this->changeArr($f[1]);
            }
            $this->params['filters'] = $list;
        }
        return $this;
    }

    function changeArr($value)
    {
        $arr = explode(',', $value);
        foreach ($arr as $key => $a) {
            $str = explode('|', $a);
            $list[$key]['value'] = $str[0];
            $list[$key]['title'] = $str[1];
        }
        return $list;
    }

    public function setPages($pages)
    {
        if (!empty($pages)) {
            $this->params['pages'] = $pages;
        }
        return $this;
    }

    public function setOps($ops)
    {
        if (!empty($ops)) {
            $ops = is_array($ops) ? $ops : explode(',', $ops);
            $this->params['ops'] = $ops;
        }
        return $this;
    }

    //模板渲染
    public function fetch($template = '', $params = [], $replace = [], $config = [])
    {
        if (!empty($params)) {
            $this->params['datas'] = array_merge($this->params['datas'], $params);
        }
        // 模板设置
        if ($template != '') {
            $this->templates = $template;
        }
        //面包屑导航
        makeUrls($this->params['title'],1);
        session('title',$this->params['title']);
        //列数
        $this->params['rows'] = count($this->params['columns']) + count($this->params['ops']) + 1;
        // 实例化视图并渲染
        return parent::fetch($this->templates, $this->params, $replace, $config);
    }

}