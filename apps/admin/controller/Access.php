<?php
/**
 * Created by PhpStorm.
 * User: Xiao（229622916@qq.com）
 * Date: 2018/2/5
 * Time: 15:17
 * Intro:
 */
namespace app\admin\controller;

use app\common\builder\Xbuilder;
use app\admin\model\Access as AccessModel;

class Access extends Base
{

    public function index()
    {
        //校验方法
        $rules = Db('auth_rule')->select();
        foreach ($rules as $key => $r) {
            AccessModel::yzFunction($r['group'], $r['title'], $r['id']);
        }
        $lists = AccessModel::order('id desc')->select();
        return Xbuilder::make('table')
            ->setTitle('用户组管理')
            ->setButtons('add,enable,disable,delete,rules')
            ->setColumns(['title|名称', 'status|状态'])
            ->setOps('rules,edit,del')
            ->setDatas($lists)
            ->fetch();
    }


    public function add()
    {
        if ($this->request->isPost()) {
            $datas = input();
            if (isset($datas['status'])) {
                $datas['status'] = 1;
            } else {
                $datas['status'] = 0;
            }
            //验证规则
            $validateRes = $this->validate($datas, 'Access');
            if (true !== $validateRes) {
                $this->error($validateRes);
            }
            $res = AccessModel::create($datas);
            if ($res) {
                $this->success('添加成功~', url('index'));
            } else {
                $this->error('添加失败~');
            }
        }
        return Xbuilder::make('form')
            ->setTitle('新增用户组')
            ->setFormData(
                [
                    ['name' => 'title', 'title' => '用户组名', 'type' => 'text', 'value' => '', 'tips' => "用户组名，如 <code>管理员</code>"],
                    ['name' => 'status', 'title' => '状态', 'type' => 'switch', 'value' => 1],
                ]
            )
            ->fetch();
    }


    public function edit()
    {
        if ($this->request->isPost()) {
            $datas = input();
            if (isset($datas['status'])) {
                $datas['status'] = 1;
            } else {
                $datas['status'] = 0;
            }
            //验证规则
            $validateRes = $this->validate($datas, 'Access');
            if (true !== $validateRes) {
                $this->error($validateRes);
            }
            $res = AccessModel::update($datas);
            if ($res) {
                $this->success('修改成功~', url('index'));
            } else {
                $this->error('修改失败~');
            }
        }
        $info = AccessModel::where(array('id' => input('id')))->find();
        return Xbuilder::make('form')
            ->setTitle('修改用户组')
            ->setFormData(
                [
                    ['name' => 'title', 'title' => '用户组名', 'type' => 'text', 'value' => $info['title'], 'tips' => "用户组名，如 <code>管理员</code>"],
                    ['name' => 'status', 'title' => '状态', 'type' => 'switch', 'value' => $info['status']],
                ]
            )
            ->fetch();
    }

    //更新状态
    public function changeStatus($id, $data)
    {
        $res = AccessModel::where(array('id' => $id))->setField('status', $data);
        if ($res) {
            $this->success('操作成功~');
        } else {
            $this->error('操作失败~');
        }
    }


    //批量启用
    public function enable($ids)
    {
        $map['id'] = array('in', $ids);
        $res = AccessModel::where($map)->setField('status', 1);
        if ($res) {
            $this->success('操作成功~');
        } else {
            $this->error('操作失败~');
        }
    }


    //批量禁用
    public function disable($ids)
    {
        $map['id'] = array('in', $ids);
        $res = AccessModel::where($map)->setField('status', 0);
        if ($res) {
            $this->success('操作成功~');
        } else {
            $this->error('操作失败~');
        }
    }


    //批量删除
    public function delete($id)
    {
        if (is_array($id)) {
            $map['id'] = array('in', $id);
        } else {
            $map['id'] = $id;
        }
        $res = AccessModel::where($map)->delete();
        if ($res) {
            $this->success('操作成功~');
        } else {
            $this->error('操作失败~');
        }
    }


    //生成节点Url
    public function upRules($modoule = 'admin')
    {
        $lists = AccessModel::makeRule();
        foreach ($lists as $key => $l) {
            foreach ($l['list'] as $k => $v) {
                $r['group'] = $l['name'];
                $r['name'] = $url = $modoule . '/' . $l['name'] . '/' . $v;
                $r['title'] = $v;
                $r['type'] = 1;
                $r['status'] = 1;
                $count = Db('auth_rule')->where(array('name' => $url))->count();
                if ($count == 0) {
                    Db('auth_rule')->insert($r);
                }
            }
        }
        $this->success('操作成功~', url('index'));
    }


    //权限管理
    public function rules()
    {
        if ($this->request->isPost()) {
            $datas = input();
            $str = array();
            foreach ($datas['rule'] as $r) {
                $str[] = $r;
            }
            $str = join(',', $str);
            $res = AccessModel::where(array('id' => $datas['id']))->setField('rules', $str);
            if ($res) {
                $this->success('操作成功~', url('admin/access/index'));
            } else {
                $this->error('操作失败~');
            }
        }
        //用户权限
        $auth = AccessModel::where(array('id' => input('id')))->value('rules');
        //权限列表
        $row = Db('auth_rule')->order('id asc')->select();
        $list = array();
        foreach ($row as $key => $value) {
            $list[$value['group']][$key] = $value;
            if (in_array($value['id'], explode(',', $auth))) {
                $list[$value['group']][$key]['on'] = 1;
            } else {
                $list[$value['group']][$key]['on'] = 0;
            }
        }
        $this->assign('auth', $auth);
        $this->assign('list', $list);
        $this->assign('title', '权限管理');
        $this->assign('id', input('id'));
        //面包屑导航
        makeUrls('权限管理', 1);
        session('title', '权限管理');
        return $this->fetch();
    }
}