<?php
/**
 * Created by PhpStorm.
 * User: Xiao（229622916@qq.com）
 * Date: 2018/1/20
 * Time: 15:15
 * Intro:
 */
namespace app\admin\controller;

use app\admin\model\Category as CategoryModel;
use app\common\builder\Xbuilder;

class Category extends Base
{


    public function index()
    {
        $list = CategoryModel::where(array('isdel' => 1))->field('id,pid,catname,status')->select();
        $list = CategoryModel::toTree($list);
        $this->assign('tree', json_encode($list));
        $this->assign('title', '栏目管理');
        //面包屑导航
        makeUrls('栏目管理', 1);
        session('title', '栏目管理');
        return $this->fetch();
    }

    public function add()
    {
        if ($this->request->isPost()) {
            $datas = input();
            if (input('pid')) {
                $datas['moduleid'] = CategoryModel::getCateModuleId(input('pid'));
            }
            //验证规则
            $validateRes = $this->validate($datas, 'Category');
            if (true !== $validateRes) {
                $this->error($validateRes);
            }
            $res = CategoryModel::create($datas);
            if ($res) {
                $this->success('添加成功~', url('index'));
            } else {
                $this->error('添加失败~');
            }
        }
        if (input('pid')) {
            $title = '新增子栏目';
            $module = '';
            $menu = ['name' => 'moduleid', 'title' => '上级栏目', 'type' => 'select', 'value' => input('pid'), 'options' => getCates()];
        } else {
            $title = '新增栏目';
            $module = ['name' => 'moduleid', 'title' => '内容模型', 'type' => 'select', 'options' => getModules()];
            $menu = '';
        }
        return Xbuilder::make('form')
            ->setTitle($title)
            ->setFormData(
                [
                    $module,
                    $menu,
                    ['name' => 'catname', 'title' => '栏目名称', 'type' => 'text', 'tips' => "一般由中文组成"],
                    ['name' => 'catdir', 'title' => '栏目目录', 'type' => 'text', 'tips' => "由字母和下划线组成（方法名），如<code>news</code>"],
                    ['name' => 'seo_title', 'title' => 'SEO标题', 'type' => 'text'],
                    ['name' => 'seo_key', 'title' => 'SEO关键字', 'type' => 'text'],
                    ['name' => 'seo_desc', 'title' => 'SEO描述', 'type' => 'textarea'],
                ]
            )
            ->fetch();
    }


    public function edit()
    {
        if ($this->request->isPost()) {
            $datas = input();
            $datas['moduleid'] = CategoryModel::getCateModuleId(input('pid'));
            //验证规则
            $validateRes = $this->validate($datas, 'Category');
            if (true !== $validateRes) {
                $this->error($validateRes);
            }
            $res = CategoryModel::update($datas);
            if ($res) {
                $this->success('修改成功~', url('index'));
            } else {
                $this->error('修改失败~');
            }
        }
        $info = CategoryModel::where(array('id' => input('id')))->find();
        return Xbuilder::make('form')
            ->setTitle('修改栏目')
            ->setFormData(
                [
                    ['name' => 'pid', 'title' => '上级栏目', 'type' => 'select', 'value' => $info['pid'], 'options' => getCates()],
                    ['name' => 'catname', 'title' => '栏目名称', 'type' => 'text', 'value' => $info['catname'], 'tips' => "一般由中文组成"],
                    ['name' => 'catdir', 'title' => '栏目目录', 'type' => 'text', 'value' => $info['catdir'], 'tips' => "由字母和下划线组成（方法名），如<code>news</code>"],
                    ['name' => 'seo_title', 'title' => 'SEO标题', 'type' => 'text', 'value' => $info['seo_title']],
                    ['name' => 'seo_key', 'title' => 'SEO关键字', 'type' => 'text', 'value' => $info['seo_key']],
                    ['name' => 'seo_desc', 'title' => 'SEO描述', 'type' => 'textarea', 'value' => $info['seo_desc']],
                    ['name' => 'status', 'title' => '状态', 'type' => 'switch', 'value' => $info['status']],
                    ['name' => 'sort', 'title' => '排序', 'type' => 'text', 'value' => $info['sort']],
                ]
            )
            ->fetch();
    }

    //更新状态
    public function changeStatus($id, $data)
    {
        $list = CategoryModel::where(array('isdel' => 1))->field('id,pid,catname,status')->select();
        $list = CategoryModel::getChildsId($list, $id);
        array_push($list, $id);
        $ids = implode(',', $list);
        $map['id'] = array('in', $ids);
        $res = CategoryModel::where($map)->setField('status', $data);
        if ($res) {
            $this->success('操作成功~');
        } else {
            $this->error('操作失败~');
        }
    }

    //删除
    public function delete($id)
    {
        $list = CategoryModel::where(array('isdel' => 1))->field('id,pid,catname,status')->select();
        $list = CategoryModel::getChildsId($list, $id);
        array_push($list, $id);
        $ids = implode(',', $list);
        $map['id'] = array('in', $ids);
        $res = CategoryModel::where($map)->setField('isdel', 0);
        if ($res) {
            $this->success('操作成功~');
        } else {
            $this->error('操作失败~');
        }
    }
}