<?php

namespace app\common\business;

use app\common\model\mysql\SpecsValue as SpecsValueModel;
use think\facade\Log;
/**
* 
*/
class SpecsValue extends BaseBis {
    
    public $Model = null;

    public function __construct() {
        parent::initialize();
        $this->Model = new SpecsValueModel();
    }

    /**
     * 增加规格属性
     * @param $data
     * @return int|mixed
     * @throws \think\Exception
     */
    public function add($data){
        $data['status'] = config('status.mysql.table_normal');
        $res = ($this->Model->getResultByWhere($data))->toArray();
        if ($res) {
            throw new \think\Exception("已存在该规格属性");            
        }        
        $data['operate_user'] = $this->adminUser;
        try {
            $this->Model->save($data);
        }catch(\Exception $e){
            Log::error("SpecsValue_add".$e->getMessage());
            return 0;
        }

        return $this->Model->id;
    }

    /**
     * 获取规格id里的所有规格属性
     * @param $specs_id
     * @return array
     */
    public function getBySpecsId($specs_id)
    {
        $where = [
            "specs_id"  => $specs_id,
            'status'    => config('status.mysql.table_normal'),
        ];
        $field = "id,name";
        try {
            $result = $this->Model->getResultByWhere($where,$field);
        }catch(\Exception $e){
            Log::error("getBySpecsId_".$e->getMessage());
            return [];
        }
        return $result->toArray();
    }

    /**
     * 删除对应的规格属性
     * @param $id
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function DelById($id){
        $res = $this->Model->find($id);
        $res->status = config('status.mysql.table_delete');
        return $res->save();
    }

}