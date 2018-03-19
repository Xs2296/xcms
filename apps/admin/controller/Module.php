<?php
/**
 * Created by PhpStorm.
 * User: Xiao（229622916@qq.com）
 * Date: 2018/1/12
 * Time: 10:29
 * Intro:模型列表
 */
namespace app\admin\controller;

use app\admin\model\Module as ModuleModel;
use app\common\builder\Xbuilder;
use think\Db;

class Module extends Base
{

    public function index()
    {
        $lists = ModuleModel::where(array('isdel' => 1))->select();
        config('ModuleModel', $lists);
        return Xbuilder::make('table')
            ->setTitle('模型管理')
            ->setButtons('add,enable,disable')
            ->setColumns(['id|编号', 'title|模型名称', 'name|表名', 'status|状态'])
            ->setOps('fields,edit,del')
            ->setDatas($lists)
            ->fetch();
    }


    //添加
    public function add()
    {
        if ($this->request->isPost()) {
            $datas = input();
            $count = ModuleModel::where(array('name' => input('name'), 'isdel' => 1))->count();
            if ($count > 0) $this->error('模型已存在~');
            //验证规则
            $validateRes = $this->validate($datas, 'Module');
            if (true !== $validateRes) {
                $this->error($validateRes);
            }
            //判断表名是否已经存在
            $tables = Db::getTables();
            $tablename = config('database.prefix') . input('name');
            if (in_array($tablename, $tables)) {
                $this->error('该表已经存在~');
            }
            //新增模型
            $res = ModuleModel::create($datas);
            if (empty($res->id)) {
                $this->error('添加失败~');
            }
            //创建表
            ModuleModel::makeTable($res->id, input('title'), input('name'), input('type'));
            if ($res->id !== false) {
                $this->success('添加成功~', url('index'));
            }
        } else {
            return Xbuilder::make('form')
                ->setTitle('新增模型')
                ->setFormData(
                    [
                        ['name' => 'title', 'title' => '模型名称', 'type' => 'text', 'value' => '', 'tips' => "模型名称，如 <code>文章模型</code>"],
                        ['name' => 'name', 'title' => '表名', 'type' => 'text', 'value' => '', 'tips' => "表名，如<code>article</code>"],
                        ['name' => 'desc', 'title' => '模型描述', 'type' => 'textarea', 'value' => ''],
                        ['name' => 'type', 'title' => '表类型', 'type' => 'radio', 'value' => '2', 'options' => ['1' => '空表', '2' => '文章表'], 'tips' => "空表只有id、status等"],
                    ]
                )
                ->fetch();
        }
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
            $validateRes = $this->validate($datas, 'Module');
            if (true !== $validateRes) {
                $this->error($validateRes);
            }
            $res = ModuleModel::update($datas);
            if ($res) {
                $this->success('修改成功~', url('index'));
            } else {
                $this->error('修改失败~');
            }
        }
        $info = ModuleModel::get($id);
        return Xbuilder::make('form')
            ->setTitle('修改模型')
            ->setFormData(
                [
                    ['name' => 'title', 'title' => '模型名称', 'type' => 'text', 'value' => $info['title'], 'tips' => "模型名称，如 <code>文章模型</code>"],
                    ['name' => 'name', 'title' => '表名', 'type' => 'text', 'value' => $info['name'], 'tips' => "表名，如<code>article</code>", 'readonly' => 1],
                    ['name' => 'desc', 'title' => '模型描述', 'type' => 'textarea', 'value' => $info['desc']],
                    ['name' => 'status', 'title' => '状态', 'type' => 'switch', 'value' => $info['status']],
                ]
            )
            ->fetch();
    }

    //更新状态
    public function changeStatus($id, $data)
    {
        $res = ModuleModel::where(array('id' => $id))->setField('status', $data);
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
        $res = ModuleModel::where($map)->setField('status', 1);
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
        $res = ModuleModel::where($map)->setField('status', 0);
        if ($res) {
            $this->success('操作成功~');
        } else {
            $this->error('操作失败~');
        }
    }


    //删除
    public function delete($id)
    {
        //删表
        $info = ModuleModel::where(array('id' => $id))->find();
        $table = config('database.prefix') . $info['name'];
        Db::execute(" ALTER  TABLE `" . $table . "` RENAME TO `" . $table . '_' . date('Ymd') . "` ");
        Db('Fields')->where(array('moduleid' => $id))->setField('isdel', 0);
        //修改状态
        $res = ModuleModel::where(array('id' => $id))->setField('isdel', 0);
        if ($res) {
            $this->success('操作成功~');
        } else {
            $this->error('操作失败~');
        }
    }

}