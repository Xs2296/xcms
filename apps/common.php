<?php
//公用方法
use app\admin\model\Admin as AdminModel;
use app\admin\model\Category as CategoryModel;

//判断用户是否登录
function isLogin()
{
    $admin_auth = session('admin_auth');
    if (empty($admin_auth)) {
        if (cookie('?uuid') && cookie('?admin_token')) {
            $admin = AdminModel::get(cookie('uuid'));
            if ($admin) {
                $cookieAuth = dataSign($admin['uuid'] . $admin['name'] . $admin['last_login_time']);
                if (cookie('admin_token') == $cookieAuth) {
                    AdminModel::autoLogin($admin);
                    return $admin['uuid'];
                }
            }
        };
        return 0;
    } else {
        return session('admin_auth_sign') == dataSign($admin_auth) ? $admin_auth['uuid'] : 0;
    }
}

//数据签名
function dataSign($data = [])
{
    // 数据类型检测
    if (!is_array($data)) {
        $data = (array)$data;
    }
    // 排序
    ksort($data);
    // url编码并生成query字符串
    $code = http_build_query($data);
    // 生成签名
    $sign = sha1($code);
    return $sign;
}

//生成UUID
function getUuid()
{
    $chars = md5(uniqid(mt_rand(), true));
    $uuid = substr($chars, 4, 8) . substr(time(), 6, 4) . substr($chars, 12, 4);
    return $uuid;
}

//生成随机字符串
function randStr($length)
{
    $str = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $strlen = 62;
    while ($length > $strlen) {
        $str .= $str;
        $strlen += 62;
    }
    $str = str_shuffle($str);
    return substr($str, 0, $length);
}

//模板行为钩子
function hooks($name = '', $params = null, $extra = null, $once = false)
{
    \think\Hook::listen($name, $params, $extra, $once);
}

//过滤筛选条件
function getFilters($arr = [])
{
    if (empty($arr)) {
        return false;
    }
    foreach ($arr as $key => $v) {
        if ($key != 'pagesize' && $v != '99' && $key != 'page') {
            $map[$key] = $v;
        }
    }
    return $map;
}

//数组转字符串
function changeStr($arr)
{
    $str = '';
    foreach ($arr as $a) {
        $str .= $a . ',';
    }
    return rtrim($str, ',');
}

//转换数据大小
function dataSize($size, $digits = 2)
{
    $unit = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    $base = 1024;
    $i = floor(log($size, $base));
    $n = count($unit);
    if ($i >= $n) {
        $i = $n - 1;
    }
    if ($size == 0) {
        return '0' . ' ' . $unit[0];
    } else {
        return round($size / pow($base, $i), $digits) . ' ' . $unit[$i];
    }

}

//配置解析
function parse_attr($value = '')
{
    $array = preg_split('/[,;\r\n]+/', trim($value, ",;\r\n"));
    if (strpos($value, ':')) {
        $value = array();
        foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k] = $v;
        }
    } else {
        $value = $array;
    }
    return $value;
}

//获取数据类型
function getItemType($item)
{
    $items = config('system_config')['items'];
    foreach ($items as $key => $value) {
        if ($key == $item) {
            $str = $value;
        }
    }
    return $str;
}

//数据类型
function getItems()
{
    $items = config('system_config')['items'];
    foreach ($items as $key => $i) {
        $lists[$key]['id'] = $key;
        $lists[$key]['title'] = $i;
        $lists[$key]['show'] = 1;
    }
    return $lists;
}

//数据类型
function getGroup()
{
    $items = Db('auth_group')->select();
    foreach ($items as $key => $i) {
        $lists[$key]['id'] = $i['id'];
        $lists[$key]['title'] = $i['title'];
        $lists[$key]['show'] = 1;
    }
    return $lists;
}

//验证规则
function getRules()
{
    $rules = config('system_config')['rules'];
    foreach ($rules as $key => $r) {
        $lists[$key]['id'] = $key;
        $lists[$key]['title'] = $r;
        $lists[$key]['show'] = 1;
    }
    return $lists;
}


//获取模型列表
function getModules()
{
    $modules = config('modules');
    foreach ($modules as $key => $m) {
        $modules[$key]['show'] = 1;
    }
    return $modules;
}

//获取栏目列表
function getCates()
{
    $cates = CategoryModel::where(array('isdel' => 1, 'status' => 1))->field('id,pid,catname')->select();
    $cates = CategoryModel::toList($cates);
    foreach ($cates as $key => $c) {
        $lists[$key]['id'] = $c['id'];
        $lists[$key]['title'] = $c['display'] . $c['catname'];
        if ($c['childs'] == 0) {
            $lists[$key]['show'] = 1;
        } else {
            $lists[$key]['show'] = 0;
        }
    }
    return $lists;
}

//获取栏目名称
function getCateName($id)
{
    return CategoryModel::where(array('id' => $id))->value('catname');
}

//面包屑
function makeUrls($title, $type)
{
    $url = request()->url();
    if ($type == 1) {
        $ursl[0]['url'] = $url;
        $ursl[0]['title'] = $title;
        session('url', $url);
        session('title', $title);
    } else {
        $ursl[0]['url'] = session('url');
        $ursl[0]['title'] = session('title');
        $ursl[1]['url'] = $url;
        $ursl[1]['title'] = $title;
    }
    session('urls', $ursl);
}

function getRuleName($str)
{
    $arr = ['index' => '列表', 'add' => '添加', 'edit' => '修改', 'changeSort' => '排序', 'changeStatus' => '更改状态', 'enable' => '启用', 'disable' => '禁用', 'delete' => '删除', 'upRules' => '更新节点', 'rules' => '节点列表', 'export' => '导出', 'import' => '导入', 'optimize' => '优化', 'repair' => '修复', 'logout' => '退出登录', 'pic' => '上传', 'waterMark' => '水印', 'makeThumb' => '缩略图', 'remove' => '移除图片', 'clearCache' => '清理缓存', 'lock' => '锁屏', 'unlock' => '解屏'];
    return $arr[$str];
}