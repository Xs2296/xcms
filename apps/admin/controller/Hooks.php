<?php
/**
 * Created by PhpStorm.
 * User: s____
 * Date: 2017/12/3
 * Time: 13:52
 */
namespace app\admin\controller;

use app\common\builder\Xbuilder;

class Hooks extends Base
{

    //钩子管理
    public function index()
    {
        $pageSize = input('pagesize') ? input('pagesize') : Config('paginate.list_rows');
        $map = getFilters(input());
        $map['isdel'] = 1;
        $lists = Db('Hooks')->where($map)->order('id desc')->paginate($pageSize);
        $page = $lists->render();
        return Xbuilder::make('table')
            ->setTitle('钩子管理')
            ->setButtons('add,enable,disable,delete')
            ->setColumns(['name|名称', 'title|描述', 'plugin|标识', 'status|状态', 'sort|排序'])
            ->setFilters([
                ['status', '99|状态管理,0|禁用,1|启用']
            ])
            ->setOps('edit,del')
            ->setDatas($lists)
            ->setPages($page)
            ->fetch();
    }


    //添加
    public function add()
    {
        if ($this->request->isPost()) {
            $datas = input();
            $count = Db('Hooks')->where(array('name' => $datas['name'], 'plugin' => $datas['plugin'], 'isdel' => 1))->count();
            if ($count > 0) $this->error('该插件已存在~');
            //验证规则
            $validateRes = $this->validate($datas, 'Hooks');
            if (true !== $validateRes) {
                $this->error($validateRes);
            }
            //校验插件
            $class = '\\plugins\\' . $datas["plugin"] . '\\' . $datas["plugin"];
            if (!class_exists($class)) {
                $this->error('控制器不存在~');
            } else {
                $method = new $class;
                if (!method_exists($method, $datas["name"])) {
                    $this->error('方法不存在~');
                }
            }
            $datas['create_time'] = time();
            $res = Db('Hooks')->insertGetId($datas);
            if ($res) {
                $this->success('添加成功~', url('index'));
            } else {
                $this->error('添加失败~');
            }
        }
        return Xbuilder::make('form')
            ->setTitle('新增钩子')
            ->setFormData(
                [
                    ['name' => 'plugin', 'title' => '所属插件', 'type' => 'text', 'value' => '', 'tips' => "驼峰命名（控制器名），如 <code>System</code>"],
                    ['name' => 'name', 'title' => '钩子名称', 'type' => 'text', 'value' => '', 'tips' => "由字母和下划线组成（方法名），如<code>index</code>"],
                    ['name' => 'title', 'title' => '钩子描述', 'type' => 'text', 'value' => '', 'tips' => "一般由中文组成，仅用于显示"],
                ]
            )
            ->fetch();
    }


    //修改
    public function edit($id = 0)
    {
        if ($id == 0) $this->error('参数错误~');
        if ($this->request->isPost()) {
            $datas = input();
            if (isset($datas['status'])) {
                $datas['status'] = 1;
            } else {
                $datas['status'] = 0;
            }
            //验证规则
            $validateRes = $this->validate($datas, 'Hooks');
            if (true !== $validateRes) {
                $this->error($validateRes);
            }
            //校验插件
            $class = '\\plugins\\' . $datas["plugin"] . '\\' . $datas["plugin"];
            if (!class_exists($class)) {
                $this->error('控制器不存在~');
            } else {
                $method = new $class;
                if (!method_exists($method, $datas["name"])) {
                    $this->error('方法不存在~');
                }
            }
            $res = Db('Hooks')->update($datas);
            if ($res) {
                $this->success('修改成功~', url('index'));
            } else {
                $this->error('修改失败~');
            }
        }
        $info = Db('Hooks')->find($id);
        return Xbuilder::make('form')
            ->setTitle('修改钩子')
            ->setFormData(
                [
                    ['name' => 'plugin', 'title' => '所属插件', 'type' => 'text', 'value' => $info['plugin'], 'tips' => "驼峰命名（控制器名），如 <code>System</code>"],
                    ['name' => 'name', 'title' => '钩子名称', 'type' => 'text', 'value' => $info['name'], 'tips' => "由字母和下划线组成（方法名），如<code>index</code>"],
                    ['name' => 'title', 'title' => '钩子描述', 'type' => 'text', 'value' => $info['title'], 'tips' => "一般由中文组成，仅用于显示"],
                    ['name' => 'status', 'title' => '状态', 'type' => 'switch', 'value' => $info['status']],
                    ['name' => 'sort', 'title' => '排序', 'type' => 'text', 'value' => $info['sort']],
                ]
            )
            ->fetch();
    }


    //更新状态
    public function changeStatus($id, $data)
    {
        $res = Db('Hooks')->where(array('id' => $id))->setField('status', $data);
        if ($res) {
            cache('hooks', null);
            $this->success('操作成功~');
        } else {
            $this->error('操作失败~');
        }
    }


    //更新排序
    public function changeSort($id, $data)
    {
        $res = Db('Hooks')->where(array('id' => $id))->setField('sort', $data);
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
        $res = Db('Hooks')->where($map)->setField('status', 1);
        if ($res) {
            cache('hooks', null);
            $this->success('操作成功~');
        } else {
            $this->error('操作失败~');
        }
    }


    //批量禁用
    public function disable($ids)
    {
        $map['id'] = array('in', $ids);
        $res = Db('Hooks')->where($map)->setField('status', 0);
        if ($res) {
            cache('hooks', null);
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
        $res = Db('Hooks')->where($map)->setField('isdel', 0);
        if ($res) {
            cache('hooks', null);
            $this->success('操作成功~');
        } else {
            $this->error('操作失败~');
        }
    }
}