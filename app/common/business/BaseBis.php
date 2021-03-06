<?php

namespace app\common\business;
use think\facade\Session;
use think\facade\Log;

/**
* 基础BaseBis
*/
class BaseBis {

    public $adminUser = '';

    public function initialize()
    {
        if (strstr("admin",request()->root())){
            $adminUser = Session::get(config('admin.session_admin'));
            if ($adminUser && isset($adminUser['username'])) {
                $this->adminUser = $adminUser['username'];
            }
        }
    }

    public function add($data) {
        $data['status'] = config('status.mysql.table_normal');
        try {
            $this->model->save($data);
        } catch (\Exception $e) {
            Log::error("数据插入失败".$e->getMessage());
            throwE($e, $e->getCode(),"新增商品失败");
        }
        return $this->model->id;
    }

    public function getLists($data, $num) {
        $likeKeys = [];
        if (!empty($data)){
            $likeKeys = array_keys($data);
        }
        try {
            $list = $this->model->getLists($data, $num,$likeKeys);
            $result = $list->toArray();
        } catch (\Exception $e) {
            return \app\common\lib\Arr::getPaginateDefaultData($num);
        }

        //$result['render'] = $list->render();
        return $result;
    }


    /**
     * 更新记录，data需要传入主键id
     * @param $data
     * @return mixed
     * @throws \think\Exception
     */
    public function updateById($data) {
        if (empty($data['id'])) {
            throw new \think\Exception("id异常");
        }
        $res = $this->model->getFieldById($data['id']);
        if (empty($res)) {
            throw new \think\Exception("不存在该记录");
        }

        $data['update_time'] = time();

        try {
            $result = $res->save($data);
        } catch (\Exception $e) {
            throwE($e, config('status.update_error'), "更新数据失败");
        }
        return $result;
    }

    /**
     * 根据id获取单条信息
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
        try {
            $info = $this->model->getFieldById($id);
        }catch (\Exception $e) {
            throwE($e, $e->getCode(), "当前记录不存在");
        }

        if (empty($info)) {
            throw new \think\Exception("不存在该记录");
        }

        return $info->toArray();
    }
}