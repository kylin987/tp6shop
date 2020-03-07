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
     * @param $id
     * @param string $field
     * @param bool $isAll
     * @return array|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getFieldById($id, $field = "*", $isAll = true) {
        $where = [];
        if (!$isAll) {
            $where[] = ["status", "<>", config('status.mysql.table_delete')];
        }
        return $this->field($field)->find($id);
         
    }

    /**
     * 根据查询条件返回对应结果
     * @param array $where
     * @param string $field
     * @param array $order
     * @return \think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getResultByWhere($where = [], $field = "*",$order = []) {
        $res = $this->where($where)
            ->field($field)
            ->order($order)
            ->select();
        return $res;
    }

    /**
     * 根据id更新表数据
     * @param $id
     * @param $data
     * @return bool
     */
    public function updateById($id, $data) {
        $data['update_time'] = time();
        return $this->where(['id'=>$id])->save($data);
    }

}