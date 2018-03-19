<?php
/**
 * Created by PhpStorm.
 * User: Xiao（229622916@qq.com）
 * Date: 2018/1/18
 * Time: 17:09
 * Intro: cms管理
 */
namespace app\admin\controller;

use app\admin\model\Module as ModuleModel;
use app\common\builder\Xbuilder;

class Xcms extends Base
{

    public function index($moduleid = 0)
    {
        //模型id
        if ($moduleid == 0) $this->error('参数错误~');
        $this->assign('moduleid', $moduleid);
        $info = ModuleModel::where(array('id' => $moduleid))->find();
        if (!$info) {
            $this->error('模型不存在~');
        }
        //构造条件
        $pageSize = input('pagesize') ? input('pagesize') : Config('paginate.list_rows');
        $map = getFilters(input());
        //过滤条件
        unset($map['moduleid']);
        $map['isdel'] = 1;
        $lists = Db($info['name'])->where($map)->order('id desc')->paginate($pageSize);
        return Xbuilder::make('table')
            ->setTitle($info['title'] . '列表')
            ->setButtons('add')
            ->setSearch(['name' => 'search', 'title' => '搜索文章标题'])
            ->setColumns(['id|编号', 'cates|所属栏目', 'title|标题', 'status|状态', 'create_time|发布时间'])
            ->setOps('edit,del')
            ->setDatas($lists)
            ->setPages($lists->render())
            ->fetch();
    }


    public function add()
    {
        $moduleid = input('moduleid');
        if ($moduleid == 0) $this->error('参数错误~');
        $this->assign('moduleid', $moduleid);
        //对应表
        $table = ModuleModel::where(array('id' => $moduleid))->value('name');
        if (!$table) {
            $this->error('模型不存在~');
        }
        //获取字段
        $XcmsModel = new \app\admin\model\Xcms();
        $formDatas = $XcmsModel->getAddFields($moduleid);
        //验证规则
        if ($this->request->isPost()) {
            $datas = input();
            $validateRes = $this->validate($datas, 'Xcms');
            if (true !== $validateRes) {
                $this->error($validateRes);
            }
            //处理时间戳
            foreach ($datas as $key => $value) {
                $type = $XcmsModel->where(array('name' => $key, 'moduleid' => $datas['moduleid']))->value('type');
                if ($type == 'date') {
                    $datas[$key] = strtotime($value);
                }
            }
            //保存数据
            $datas['create_time'] = time();
            $res = Db($table)->strict(false)->insertGetId($datas);
            if ($res) {
                $this->success('添加成功~', url('index', array('moduleid' => $datas['moduleid'])));
            } else {
                $this->error('添加失败~');
            }
        }
        return Xbuilder::make('form')
            ->setTitle('新增文章')
            ->setFormData($formDatas)
            ->fetch();
    }


    public function edit()
    {
        $moduleid = input('moduleid');
        if ($moduleid == 0) $this->error('参数错误~');
        //对应表
        $table = ModuleModel::where(array('id' => $moduleid))->value('name');
        if (!$table) {
            $this->error('模型不存在~');
        }
        //获取字段
        $XcmsModel = new \app\admin\model\Xcms();
        $formDatas = $XcmsModel->getEditFields($moduleid, $table, input('id'));
        //验证规则
        if ($this->request->isPost()) {
            $datas = input();
            $validateRes = $this->validate($datas, 'Xcms');
            if (true !== $validateRes) {
                $this->error($validateRes);
            }
            //处理时间戳
            foreach ($datas as $key => $value) {
                $type = $XcmsModel->where(array('name' => $key, 'moduleid' => $datas['moduleid']))->value('type');
                if ($type == 'date') {
                    $datas[$key] = strtotime($value);
                }
            }
            //保存数据
            $datas['update_time'] = time();
            $res = Db($table)->strict(false)->update($datas);
            if ($res) {
                $this->success('修改成功~', url('index', array('moduleid' => $datas['moduleid'])));
            } else {
                $this->error('修改失败~');
            }
        }
        return Xbuilder::make('form')
            ->setTitle('修改文章')
            ->setFormData($formDatas)
            ->fetch();
    }


    //更新状态
    public function changeStatus($id, $data, $moduleid)
    {
        if ($moduleid == 0) $this->error('参数错误~');
        $table = ModuleModel::where(array('id' => $moduleid))->value('name');
        $res = Db($table)->where(array('id' => $id))->setField('status', $data);
        if ($res) {
            $this->success('操作成功~');
        } else {
            $this->error('操作失败~');
        }
    }

    public function delete($id, $moduleid)
    {
        if ($moduleid == 0) $this->error('参数错误~');
        $table = ModuleModel::where(array('id' => $moduleid))->value('name');
        $res = Db($table)->where(array('id' => $id))->setField('isdel', 0);
        if ($res) {
            $this->success('操作成功~');
        } else {
            $this->error('操作失败~');
        }
    }

}