<?php
/**
 * Created by PhpStorm.
 * User: Xiao（229622916@qq.com）
 * Date: 2018/1/3
 * Time: 16:56
 * Intro:
 */
namespace app\admin\controller;

use app\admin\model\Config as ConfigModel;
use app\common\builder\Xbuilder;

class Config extends Base
{

    public function index($group = 'system')
    {
        // 配置分组信息
        $list_group = config('system_config')['groups'];
        $tab_list = [];
        foreach ($list_group as $key => $value) {
            $tab_list[$key]['title'] = $value;
            $tab_list[$key]['url'] = url('index', ['group' => $key]);
        }
        $this->assign('tabs', $tab_list);
        // 查询条件
        $pageSize = input('pagesize') ? input('pagesize') : Config('paginate.list_rows');
        $map = getFilters(input());
        $map['group'] = $group;
        $map['isdel'] = 1;
        // 数据列表
        $lists = ConfigModel::where($map)->order('sort desc,id desc')->paginate($pageSize);
        $page = $lists->render();
        return Xbuilder::make('table')
            ->setTitle('配置管理')
            ->setButtons('add,enable,disable,delete')
            ->setColumns(['title|标题', 'name|名称', 'type|配置类型', 'status|状态', 'sort|排序'])
            ->setFilters([
                ['status', '99|状态管理,0|启用,1|禁用']
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
            $count = ConfigModel::where(array('group' => $datas['group'], 'name' => $datas['name'], 'isdel' => 1))->count();
            if ($count > 0) $this->error('该配置已存在~');
            //验证规则
            $validateRes = $this->validate($datas, 'Config');
            if (true !== $validateRes) {
                $this->error($validateRes);
            }
            $datas['create_time'] = time();
            $res = ConfigModel::create($datas);
            if ($res) {
                cache('system_config', null);
                $this->success('添加成功', url('index'));
            } else {
                $this->error('添加失败');
            }
        }
        return Xbuilder::make('form')
            ->setTitle('新增配置')
            ->setFormData(
                [
                    ['name' => 'group', 'title' => '配置分组', 'type' => 'radio', 'value' => 'system', 'options' => config('system_config')['groups']],
                    ['name' => 'type', 'title' => '配置类型', 'type' => 'select', 'value' => 'text', 'options' => getItems()],
                    ['name' => 'name', 'title' => '配置名称', 'type' => 'text', 'value' => '', 'tips' => "由英文字母和下划线组成，如 <code>web_title</code>"],
                    ['name' => 'title', 'title' => '配置标题', 'type' => 'text', 'value' => '', 'tips' => "一般由中文组成，仅用于显示"],
                    ['name' => 'value', 'title' => '配置值', 'type' => 'textarea', 'value' => '', 'tips' => "配置的具体值"],
                    ['name' => 'options', 'title' => '配置项', 'type' => 'textarea', 'value' => '', 'tips' => "用于单选、多选、下拉、联动等类型"],
                    ['name' => 'tips', 'title' => '配置说明', 'type' => 'text', 'value' => '', 'tips' => "配置提示说明"],
                    ['name' => 'sort', 'title' => '排序', 'type' => 'text', 'value' => '0'],
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
            $validateRes = $this->validate($datas, 'Config');
            if (true !== $validateRes) {
                $this->error($validateRes);
            }
            $res = ConfigModel::update($datas);
            if ($res) {
                cache('system_config', null);
                $this->success('修改成功~', url('index'));
            } else {
                $this->error('修改失败~');
            }
        }
        $info = ConfigModel::get($id);
        return Xbuilder::make('form')
            ->setTitle('修改配置')
            ->setFormData(
                [
                    ['name' => 'group', 'title' => '配置分组', 'type' => 'radio', 'value' => $info['group'], 'options' => config('system_config')['groups']],
                    ['name' => 'type', 'title' => '配置类型', 'type' => 'select', 'value' => $info['type'], 'options' => getItems()],
                    ['name' => 'name', 'title' => '配置名称', 'type' => 'text', 'value' => $info['name'], 'tips' => "由英文字母和下划线组成，如 <code>web_title</code>"],
                    ['name' => 'title', 'title' => '配置标题', 'type' => 'text', 'value' => $info['title'], 'tips' => "一般由中文组成，仅用于显示"],
                    ['name' => 'value', 'title' => '配置值', 'type' => 'textarea', 'value' => $info['value'], 'tips' => "配置的具体值"],
                    ['name' => 'options', 'title' => '配置项', 'type' => 'textarea', 'value' => $info['options'], 'tips' => "用于单选、多选、下拉、联动等类型"],
                    ['name' => 'tips', 'title' => '配置说明', 'type' => 'text', 'value' => $info['tips'], 'tips' => "配置提示说明"],
                    ['name' => 'status', 'title' => '状态', 'type' => 'switch', 'value' => $info['status']],
                    ['name' => 'sort', 'title' => '排序', 'type' => 'text', 'value' => $info['sort']],
                ]
            )
            ->fetch();
    }


    //更新状态
    public function changeStatus($id, $data)
    {
        $res = ConfigModel::where(array('id' => $id))->setField('status', $data);
        if ($res) {
            cache('system_config', null);
            $this->success('操作成功~');
        } else {
            $this->error('操作失败~');
        }
    }


    //更新排序
    public function changeSort($id, $data)
    {
        $res = ConfigModel::where(array('id' => $id))->setField('sort', $data);
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
        $res = ConfigModel::where($map)->setField('status', 1);
        if ($res) {
            cache('system_config', null);
            $this->success('操作成功~');
        } else {
            $this->error('操作失败~');
        }
    }


    //批量禁用
    public function disable($ids)
    {
        $map['id'] = array('in', $ids);
        $res = ConfigModel::where($map)->setField('status', 0);
        if ($res) {
            cache('system_config', null);
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
        $res = ConfigModel::where($map)->setField('isdel', 0);
        if ($res) {
            cache('system_config', null);
            $this->success('操作成功~');
        } else {
            $this->error('操作失败~');
        }
    }

}