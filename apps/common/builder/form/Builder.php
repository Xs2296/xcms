<?php
/**
 * Created by PhpStorm.
 * User: Xiao（229622916@qq.com）
 * Date: 2017/11/23
 * Time: 16:04
 * Intro:
 */
namespace app\common\builder\form;

use app\common\builder\Xbuilder;

class Builder extends Xbuilder
{

    private $templates;

    private $params = [
        'title' => '',
        'postUrl' => '',    //提交地址
        'postMethod' => 'post',    //提交方式
        'isAjax' => true,     //是否ajax提交
        'formData' => [],  //表单数据
    ];

    public function _initialize()
    {
        $this->templates = APP_PATH . 'common/builder/form/layout.html';
        $this->params['postUrl'] = $this->request->url(true);
    }

    public function setTitle($title)
    {
        if (!empty($title)) {
            $this->params['title'] = $title;
        }
        return $this;
    }

    public function setPostUrl($postUrl = '')
    {
        if ($postUrl) {
            $this->params['postUrl'] = trim($postUrl);
        }
        return $this;
    }

    public function setPostMethod($value = '')
    {
        if ($value != '') {
            $this->params['postMethod'] = $value;
            $this->params['isAjax'] = strtolower($value) == 'get' ? false : true;
        }
        return $this;
    }

    public function isAjax($isAjax = true)
    {
        $this->params['isAjax'] = $isAjax;
        return $this;
    }

    public function setFormData($formData = [])
    {
        if (!empty($formData)) {
            $this->params['formData'] = $formData;
        }
        return $this;
    }

    //模板渲染
    public function fetch($template = '', $params = [], $replace = [], $config = [])
    {
        if (!empty($params)) {
            $this->params['formData'] = array_merge($this->params['formData'], $params);
        }
        // 模板设置
        if ($template != '') {
            $this->templates = $template;
        }
        //面包屑导航
        makeUrls($this->params['title'],2);
        session('title',$this->params['title']);
        // 实例化视图并渲染
        return parent::fetch($this->templates, $this->params, $replace, $config);
    }

}