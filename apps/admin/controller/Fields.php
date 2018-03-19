<?php
/**
 * Created by PhpStorm.
 * User: Xiao（229622916@qq.com）
 * Date: 2018/1/12
 * Time: 15:10
 * Intro:字段管理
 */
namespace app\admin\controller;

use app\admin\model\Fields as FieldsModel;
use app\common\builder\Xbuilder;
use think\Db;

class Fields extends Base
{

    public function index($moduleid = 0)
    {
        if ($moduleid == 0) $this->error('参数错误~');
        $this->assign('moduleid', input('moduleid'));
        $lists = FieldsModel::where(array('moduleid' => $moduleid, 'isdel' => 1))->order('sort asc')->select();
        return Xbuilder::make('table')
            ->setTitle('字段管理')
            ->setButtons('add,enable,disable')
            ->setColumns(['title|标题', 'name|字段名', 'type|字段类型', 'status|状态', 'sort|排序'])
            ->setOps('edit,del')
            ->setDatas($lists)
            ->fetch();
    }


    public function add()
    {
        if ($this->request->isPost()) {
            $datas = input();
            if ($datas['required'] == 0) {
                unset($datas['rules']);
            }
            //验证规则
            $validateRes = $this->validate($datas, 'Fields');
            if (true !== $validateRes) {
                $this->error($validateRes);
            }
            if (($datas['type'] == 'checkbox') || ($datas['type'] == 'radio') || ($datas['type'] == 'switch') || ($datas['type'] == 'select') || ($datas['type'] == 'selects')) {
                if (empty($datas['value']) && $datas['name'] != 'cates') {
                    $this->error('请输入默认值~');
                }
                if (empty($datas['options']) && $datas['name'] != 'cates') {
                    $this->error('请输入选项~');
                }
            }
            //判断字段是否存在
            $table = Db('module')->where(array('id' => $datas['moduleid']))->value('name');
            $Fields = Db::getFields(config('database.prefix') . $table);
            foreach ($Fields as $key => $r) {
                if ($key == $datas['name']) {
                    $ishave = 1;
                }
            }
            if ($ishave) $this->error('字段已存在~');
            //保存、创建字段
            $res = FieldsModel::create($datas);
            if ($res) {
                $sql = FieldsModel::getFieldSql(config('database.prefix') . $table, 'add', $datas['name'], '', $datas['type'], $datas['title'], $datas['value'], $datas['length']);
                Db::execute($sql);
                $this->success('添加成功~', url('index', array('moduleid' => $datas['moduleid'])));
            } else {
                $this->error('添加失败~');
            }
        }
        $this->assign('required', 0);
        return Xbuilder::make('form')
            ->setTitle('新增字段')
            ->setFormData(
                [
                    ['name' => 'type', 'title' => '字段类型', 'type' => 'select', 'value' => 'text', 'options' => getItems()],
                    ['name' => 'title', 'title' => '标题', 'type' => 'text', 'value' => '', 'tips' => "一般由中文组成，仅用于显示"],
                    ['name' => 'name', 'title' => '字段名', 'type' => 'text', 'value' => '', 'tips' => "由英文字母和下划线组成，如 <code>web_title</code>"],
                    ['name' => 'tips', 'title' => '提示说明', 'type' => 'text', 'value' => '', 'tips' => "字段提示说明"],
                    ['name' => 'value', 'title' => '默认值', 'type' => 'textarea', 'value' => '', 'tips' => "默认值的具体值，如 <code>1</code>"],
                    ['name' => 'options', 'title' => '选项', 'type' => 'textarea', 'value' => '', 'tips' => "用于单选、多选、下拉、联动等类型<br/>值:选项（一行一项）如<br/><code>1:是<br/>2:否<br/></code>"],
                    ['name' => 'required', 'title' => '必填', 'type' => 'radio', 'value' => '0', 'options' => ['1' => '是', '0' => '否']],
                    ['name' => 'rules', 'title' => '验证规则', 'type' => 'select', 'options' => getRules()],
                    ['name' => 'length', 'title' => '字段长度', 'type' => 'text', 'value' => '100', 'tips' => "大于255为text类型"],
                    ['name' => 'sort', 'title' => '排序', 'type' => 'text', 'value' => '1'],
                ]
            )
            ->fetch();
    }


    public function edit($id = 0)
    {
        if ($id == 0) $this->error('参数错误~');
        $info = FieldsModel::where(array('id' => $id))->find();
        if ($this->request->isPost()) {
            $datas = input();
            if ($datas['required'] == 0) {
                $datas['rules'] = '';
            }
            //验证规则
            $validateRes = $this->validate($datas, 'Fields');
            if (true !== $validateRes) {
                $this->error($validateRes);
            }
            if (($datas['type'] == 'checkbox') || ($datas['type'] == 'radio') || ($datas['type'] == 'switch') || ($datas['type'] == 'select') || ($datas['type'] == 'selects')) {
                if (empty($datas['value']) && $datas['name'] != 'cates') {
                    $this->error('请输入默认值~');
                }
                if (empty($datas['options']) && $datas['name'] != 'cates') {
                    $this->error('请输入选项~');
                }
            }
            //判断是否有修改字段名
            $table = Db('module')->where(array('id' => $info['moduleid']))->value('name');
            if ($info['name'] != $datas['name']) {
                //判断字段是否存在
                $Fields = Db::getFields(config('database.prefix') . $table);
                foreach ($Fields as $key => $r) {
                    if ($key == $datas['name']) {
                        $ishave = 1;
                    }
                }
                if ($ishave) $this->error('字段已存在~');
                $sql = FieldsModel::getFieldSql(config('database.prefix') . $table, 'edit', $datas['name'], $info['name'], $datas['type'], $datas['title'], $datas['value'], $datas['length']);
                Db::execute($sql);
            } else {
                $sql = FieldsModel::getFieldSql(config('database.prefix') . $table, 'update', $datas['name'], '', $datas['type'], $datas['title'], $datas['value'], $datas['length']);
                Db::execute($sql);
            }
            //保存、创建字段
            $res = FieldsModel::update($datas);
            if ($res) {
                $this->success('修改成功~', url('index', array('moduleid' => $info['moduleid'])));
            } else {
                $this->error('修改失败~');
            }
        }
        $this->assign('required', $info['required']);
        return Xbuilder::make('form')
            ->setTitle('修改字段')
            ->setFormData(
                [
                    ['name' => 'type', 'title' => '字段类型', 'type' => 'select', 'value' => $info['type'], 'options' => getItems()],
                    ['name' => 'title', 'title' => '标题', 'type' => 'text', 'value' => $info['title'], 'tips' => "一般由中文组成，仅用于显示"],
                    ['name' => 'name', 'title' => '字段名', 'type' => 'text', 'value' => $info['name'], 'tips' => "由英文字母和下划线组成，如 <code>web_title</code>"],
                    ['name' => 'tips', 'title' => '提示说明', 'type' => 'text', 'value' => $info['tips'], 'tips' => "字段提示说明"],
                    ['name' => 'value', 'title' => '默认值', 'type' => 'textarea', 'value' => $info['value'], 'tips' => "默认值的具体值"],
                    ['name' => 'options', 'title' => '选项', 'type' => 'textarea', 'value' => $info['options'], 'tips' => "用于单选、多选、下拉、联动等类型"],
                    ['name' => 'required', 'title' => '必填', 'type' => 'radio', 'value' => $info['required'], 'options' => ['1' => '是', '0' => '否']],
                    ['name' => 'rules', 'title' => '验证规则', 'type' => 'select', 'options' => getRules(), 'value' => $info['rules']],
                    ['name' => 'length', 'title' => '字段长度', 'type' => 'text', 'value' => $info['length'], 'tips' => "大于255为text类型"],
                    ['name' => 'sort', 'title' => '排序', 'type' => 'text', 'value' => $info['sort']],
                ]
            )
            ->fetch();
    }

    //更新状态
    public function changeStatus($id, $data)
    {
        $res = FieldsModel::where(array('id' => $id))->setField('status', $data);
        if ($res) {
            $this->success('操作成功~');
        } else {
            $this->error('操作失败~');
        }
    }


    //更新排序
    public function changeSort($id, $data)
    {
        $res = FieldsModel::where(array('id' => $id))->setField('sort', $data);
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
        $res = FieldsModel::where($map)->setField('status', 1);
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
        $res = FieldsModel::where($map)->setField('status', 0);
        if ($res) {
            $this->success('操作成功~');
        } else {
            $this->error('操作失败~');
        }
    }

    //批量删除
    public function delete($id)
    {
        //删除字段
        $info = FieldsModel::where(array('id' => $id))->find();
        $table = Db('module')->where(array('id' => $info['moduleid']))->value('name');
        $sql = FieldsModel::delFieldSql($table, $info['name']);
        Db::execute($sql);
        //修改状态
        $res = FieldsModel::where(array('id' => $id))->setField('isdel', 0);
        if ($res) {
            $this->success('操作成功~');
        } else {
            $this->error('操作失败~');
        }
    }
}