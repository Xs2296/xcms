<?php
/**
 * Created by PhpStorm.
 * User: Xiao（229622916@qq.com）
 * Date: 2018/2/5
 * Time: 15:12
 * Intro:
 */
namespace app\admin\controller;

use app\common\builder\Xbuilder;
use app\admin\model\Admin as AdminModel;
use app\admin\model\Access as AccessModel;
use think\Request;

class User extends Base
{

    public function index()
    {
        $lists = AdminModel::where(array('isdel' => 1))->order('create_time desc')->select();
        foreach ($lists as $key => $l) {
            $lists[$key]['id'] = $l['uuid'];
            $lists[$key]['group'] = AccessModel::getGroupName($l['uuid']);
        }
        return Xbuilder::make('table')
            ->setTitle('用户管理')
            ->setButtons('add,enable,disable,delete')
            ->setColumns(['name|用户名', 'group|用户组', 'email|邮箱', 'status|状态', 'reg_ip|注册Ip', 'last_login_time|最后登录时间'])
            ->setOps('edit,del')
            ->setDatas($lists)
            ->fetch();
    }


    public function add()
    {
        if ($this->request->isPost()) {
            $datas = input();
            if (isset($datas['status'])) {
                $map['status'] = 1;
            } else {
                $map['status'] = 0;
            }
            //验证规则
            $datas['type'] = 1;
            $validateRes = $this->validate($datas, 'User');
            if (true !== $validateRes) {
                $this->error($validateRes);
            }
            $map['name'] = $datas['name'];
            $map['email'] = $datas['email'];
            $map['uuid'] = $uuid = getUuid();
            $map['salt'] = $salt = randStr(8);
            $map['password'] = Md5($datas['password'] . $salt);
            $map['reg_ip'] = Request::instance()->ip();
            $map['create_time'] = time();
            $map['last_login_time'] = time();
            $res = AdminModel::create($map);
            if ($res) {
                Db('auth_group_access')->insert(['uuid' => $uuid, 'group_id' => $datas['group']]);
                $this->success('添加成功~', url('index'));
            } else {
                $this->error('添加失败~');
            }
        }
        return Xbuilder::make('form')
            ->setTitle('新增用户')
            ->setFormData(
                [
                    ['name' => 'group', 'title' => '用户组', 'type' => 'select', 'options' => getGroup()],
                    ['name' => 'name', 'title' => '用户名', 'type' => 'text', 'value' => ''],
                    ['name' => 'password', 'title' => '密码', 'type' => 'password', 'value' => ''],
                    ['name' => 'email', 'title' => '邮箱', 'type' => 'text', 'value' => ''],
                    ['name' => 'status', 'title' => '状态', 'type' => 'switch', 'value' => 1],
                ]
            )
            ->fetch();
    }


    public function edit()
    {
        $info = AdminModel::where(array('uuid' => input('id')))->find();
        $group_id = Db('auth_group_access')->where(array('uuid' => input('id')))->value('group_id');
        if ($this->request->isPost()) {
            $datas = input();
            if (isset($datas['status'])) {
                $map['status'] = 1;
            } else {
                $map['status'] = 0;
            }
            //验证规则
            $datas['type'] = 2;
            $validateRes = $this->validate($datas, 'User');
            if (true !== $validateRes) {
                $this->error($validateRes);
            }
            $map['email'] = $datas['email'];
            if (isset($datas['password'])) {
                $map['password'] = Md5($datas['password'] . $info['salt']);
            }
            $res = AdminModel::where(array('uuid' => input('id')))->update($map);
            if ($res) {
                Db('auth_group_access')->where(['uuid' => input('id')])->setField('group_id', $datas['group']);
                $this->success('修改成功~', url('index'));
            } else {
                $this->error('修改失败~');
            }
        }
        return Xbuilder::make('form')
            ->setTitle('修改用户')
            ->setFormData(
                [
                    ['name' => 'group', 'title' => '用户组', 'type' => 'select', 'options' => getGroup(), 'value' => $group_id],
                    ['name' => 'name', 'title' => '用户名', 'type' => 'text', 'value' => $info['name'], 'readonly' => 1],
                    ['name' => 'password', 'title' => '密码', 'type' => 'password', 'tips' => "不修改密码请留空"],
                    ['name' => 'email', 'title' => '邮箱', 'type' => 'text', 'value' => $info['email']],
                    ['name' => 'status', 'title' => '状态', 'type' => 'switch', 'value' => $info['status']],
                ]
            )
            ->fetch();
    }

    //更新状态
    public function changeStatus($id, $data)
    {
        $res = AdminModel::where(array('id' => $id))->setField('status', $data);
        if ($res) {
            $this->success('操作成功~');
        } else {
            $this->error('操作失败~');
        }
    }


    //批量启用
    public function enable($ids)
    {
        $map['uuid'] = array('in', $ids);
        $res = AdminModel::where($map)->setField('status', 1);
        if ($res) {
            $this->success('操作成功~');
        } else {
            $this->error('操作失败~');
        }
    }


    //批量禁用
    public function disable($ids)
    {
        $map['uuid'] = array('in', $ids);
        $res = AdminModel::where($map)->setField('status', 0);
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
            $map['uuid'] = array('in', $id);
        } else {
            $map['uuid'] = $id;
        }
        $res = AdminModel::where($map)->setField('isdel', 0);
        if ($res) {
            $this->success('操作成功~');
        } else {
            $this->error('操作失败~');
        }
    }

}