<?php

namespace app\common\model\mysql;

use think\Model;

/**
 * 
 */
class BaseModel extends Model
{
    //开启自动添加时间戳
    protected $autoWriteTimestamp = true;

    /**
     * 根据id获取对应的字段内容
     * @param  [type] $id    [description]
     * @param  string $field [description]
     * @return [type]        [description]
     */
    public function getFieldById($id, $field = "*") {
        return $this->field($field)->find($id);
         
    }

    /**
     * 根据查询条件返回对应结果
     * @param  array  $where [description]
     * @param  string $field [description]
     * @param  array  $order [description]
     * @return [type]        [description]
     */
    public function getResultByWhere($where = [], $field = "*",$order = []) {
        $res = $this->where($where)
            ->field($field)
            ->order($order)
            ->select();
        //halt($this->getLastSql());
        return $res;
    }

}