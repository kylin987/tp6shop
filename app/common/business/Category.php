<?php

namespace app\common\business;
use app\common\model\mysql\Category as CategoryModel;

class Category {

    public $model = null;

    public function __construct() {
        $this->model = new CategoryModel();
    }

    /**
     * 增加分类
     * @param $data
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function add($data) {
        $data['status'] = config('status.mysql.table_normal');

        $res = $this->model->getCategoryByName($data['name']);
        if ($res) {
            throw new \think\Exception("分类名已存在");
        }
        try {
            $this->model->save($data);
        } catch (\Exception $e) {
            throwE($e, config('status.error'), "服务内部异常");
        }

        return $this->model->id;
    }


    /**
     * 修改栏目信息
     * @param $data
     * @return bool
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function edit($data) {
        $res = $this->model->getCategoryByName($data['name']);
        /**
        1、不修改名字，那么获得的是本栏目信息，允许修改
        2、修改为一个未用的名字，那么res为空，允许修改
        3、修改为一个已存在的，并且为别的栏目的名字，那么获得的是别的栏目信息，不允许修改
        */
        //如果为3
        if ($res && $res['id'] != $data['id']) {
            throw new \think\Exception("分类名已存在");
        }


        try {
            $result = $this->updateCategory($data);
        } catch (\Exception $e) {
            throwE($e, config('status.error'), "服务内部异常");
        }

        return $result;
    }

    /**
     * 获取所有普通栏目信息
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getNormalCategorys() {
        $field = "id, name, pid";
        $categorys = $this->model->getNormalCategorys($field);
        if (!$categorys) {
            return [];
        } else {
            return $categorys->toArray();
        }
    }

    /**
     * 获取栏目列表信息
     * @param $data
     * @param $num
     * @return array
     * @throws \think\db\exception\DbException
     */
    public function getLists($data, $num) {
        $list = $this->model->getLists($data, $num);
        if (!$list) {
            return [];
        }

        $result = $list->toArray();
        $result['render'] = $list->render();
        /**
         * 获取每个栏目下级栏目的数量的思路
         * 1、拿到当前获取的列表中的id，也就是他们下级栏目的pid
         * 2、in mysql 求count
         * 3、把count填充到列表中
         */
        $pids = array_column($result['data'], "id");
        $idCountResult = ($this->model->getChildCountInPids($pids))->toArray();
        if ($idCountResult) {
            $idCounts = [];
            foreach ($idCountResult as $countResult) {
                $idCounts[$countResult['pid']] = $countResult['count'];
            }

            foreach ($result['data'] as $k=>$v){
                $result['data'][$k]['childCount'] = $idCounts[$v['id']] ?? 0;
            }
        } else {
            foreach ($result['data'] as $k=>$v){
                $result['data'][$k]['childCount'] = 0;
            }
        }

        return $result;
    }

    /**
     * 更新栏目信息
     * @param $data
     * @return bool
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function updateCategory($data) {
        if (empty($data['id'])) {
            throw new \think\Exception("栏目id异常");
        }
        $category = $this->model->getFieldById($data['id']);
        if (empty($category)) {
            throw new \think\Exception("不存在该记录");
        }

        $data['update_time'] = time();

        try {
            $result = $category->save($data);
        } catch (\Exception $e) {
            throwE($e, config('status.update_error'), "更新数据失败");
        }
        return $result;

    }

    /**
     * 根据id获取栏目信息
     * @param $id
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getInfoById($id) {
        if (empty($id)) {
            throw new \think\Exception("id参数错误");
        }
        $category = $this->model->getFieldById($id);
        if (empty($category)) {
            throw new \think\Exception("不存在该记录");
        }

        return $category();
    }

    /**
     * 获取面包屑导航
     * @param $pid
     * @return array|bool
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getBreadNav($pid) {
        $breadTree = [];
        $field = "id,name, pid";
        while ($pid != 0) {
            $info = $this->model->getFieldById($pid,$field,true);
            if ($info) {
                $breadTree[] = $info;
                $pid = $info['pid'];
            }
        }

        $breadTree[] = ['id'=>0,'pid'=>0, 'name'=>"栏目首页"];

        return array_reverse($breadTree);
    }
}
