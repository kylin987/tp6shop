<?php

namespace app\admin\controller;

use app\BaseController;
use think\facade\View;
use app\common\business\Category as CategoryBis;
use app\common\lib\Show;

class Category extends BaseController
{
    /**
     * 栏目列表
     * @return string
     * @throws \Exception
     */
    public function index() {
        $pid = input("param.pid", 0, "intval");
        $data = [
            'pid'   => $pid,
        ];
        $num = 5;

        try {
            $categorys = (new CategoryBis())->getListsAndChildCount($data, $num);
        } catch (\Exception $e) {
            $categorys = \app\common\lib\Arr::getPaginateDefaultData($num);
        }

        //获取面包屑导航
        $breadTree = (new CategoryBis())->getBreadNav($pid);

        View::assign('categorys',$categorys);
        View::assign('pid', $pid);
        View::assign('breadTree', $breadTree);

        return View::fetch();
    }

    /**
     * 新增栏目页面
     * @return string
     * @throws \Exception
     */
    public function add() {
        try {
            $categorys = (new CategoryBis())->getNormalCategorys();
        } catch (\Exception $e) {
            $categorys = [];
        }

        return View::fetch("", [
            'categorys' => json_encode($categorys),
        ]);
    }

    /**
     * 新增/编辑栏目保存
     * @return \think\response\Json
     */
    public function save() {
        $pid = input("param.pid", 0, "intval");
        $name = input("param.name", "", "trim");
        $id = input("param.editId", "", "intval");

        $data = [
            'pid'   => $pid,
            'name'  => $name,
        ];

        $scene = 'add';

        if ($id) {
            $data['id'] = $id;
            $scene = 'edit';
        }

        $validate = (new \app\admin\validate\Category())->scene($scene);
        if (!$validate->check($data)) {
            return Show::error($validate->getError());
        }

        try {
            if ($id){
                $result = (new CategoryBis())->edit($data);
            } else {
                $result = (new CategoryBis())->add($data);
            }

        } catch (\Exception $e) {
            return Show::error($e->getError());
        }

        return Show::success($result);
    }

    public function edit() {
        $id = input("param.id", 0, "intval");
        if (empty($id)) {
            return Show::error("栏目id不存在");
        }

        $info = (new CategoryBis())->getInfoById($id);

        try {
            $categorys = (new CategoryBis())->getNormalCategorys();
        } catch (\Exception $e) {
            $categorys = [];
        }

        return View::fetch("",[
            'info'  => $info,
            'categorys' => json_encode($categorys),
        ]);
    }

    /**
     * 更新栏目排序
     * @return \think\response\Json
     */
    public function listorder() {
        $id = input("param.id", 0, "intval");
        $listorder = input("param.listorder", 0, "intval");

        $data = [
            'id'    => $id,
            'listorder' => $listorder,
        ];

        $validate = (new \app\admin\validate\Category())->scene('changeListOrder');
        if (!$validate->check($data)) {
            return Show::error($validate->getError());
        }
        try {
            $resule = (new CategoryBis())->updateById($data);
        } catch (\Exception $e) {
            return Show::error($e->getError());
        }

        if ($resule) {
            return Show::success($resule,"排序成功");
        }
        return Show::error("排序失败");

    }

    /**
     * 修改栏目状态，包括删除99
     * @return [type] [description]
     */
    public function changeStatus(){
        $id = input("param.id", 0, "intval");
        $status = input("param.status", 0, "intval");

        $data = [
            'id'    => $id,
            'status' => $status,
        ];

        $validate = (new \app\admin\validate\Category())->scene('changeStatus');
        if (!$validate->check($data)) {
            return Show::error($validate->getError());
        }

        if (!in_array($status, \app\common\lib\Status::getTableStatus())) {
            return Show::error("参数错误");
        }

        try {
            $resule = (new CategoryBis())->updateById($data);
        } catch (\Exception $e) {
            return Show::error($e->getMessage());
        }

        if ($resule) {
            return Show::success($resule, "更新成功");
        }
        return Show::error("更新失败");
    }

    public function dialog(){
        //获取正常的一级分类数据
        $result = (new CategoryBis())->getNormalByPid();
        return view("",[
            "categorys" => json_encode($result),
        ]);
    }

    /**
     * 根据pid获取下级栏目信息
     * @return [type] [description]
     */
    public function getByPid()
    {
        $pid = input("param.pid", 0, "intval");
        $result = (new CategoryBis())->getNormalByPid($pid);
        return Show::success($result);
    }
}