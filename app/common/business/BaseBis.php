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

        $adminUser = Session::get(config('admin.session_admin'));
        if ($adminUser && isset($adminUser['username'])) {
            $this->adminUser = $adminUser['username'];
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
}