<?php

namespace app\common\model\mysql;

use think\Model;

/**
 * 
 */
class Category extends Model
{
    //开启自动添加时间戳
    protected $autoWriteTimestamp = true;


    /**
     * 根据分类名称查找分类信息
     * @param $name
     * @return array|bool|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getCategoryByName($name) {
        if (empty($name)) {
            return false;
        }

        $where = [
            'name'  => trim($name),
        ];

        return $this->where($where)->find();
    }

    /**
     * 获取所有栏目信息
     * @param string $field
     * @return \think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getNormalCategorys($field = "*") {
        $where = [
            'status'    => config('status.mysql.table_normal'),
        ];

        return $this->where($where)->field($field)->select();
    }

    /**
     * 获取栏目列表
     * @param $where
     * @param $num
     * @return \think\Paginator
     * @throws \think\db\exception\DbException
     */
    public function getLists($where, $num) {
        $order = [
            "listorder" => "desc",
            "id"        => "asc",
        ];
        $result = $this->where("status", "<>", config('status.mysql.table_delete'))
            ->where($where)
            ->order($order)
            ->paginate($num);
        return $result;
    }

    /**
     * 获取下级栏目数量
     * @param $pids
     * @return mixed
     */
    public function getChildCountInPids($pids) {
        $where[] = ["pid", "in", $pids];
        $where[] = ["status", "<>", config('status.mysql.table_delete')];

        $res = $this->where($where)
            ->field(["pid", "count(*) as count"])
            ->group("pid")
            ->select();
        //halt($this->getLastSql());
        return $res;
    }

    /**
     * 根据id获取栏目相关字段信息
     * @param $id
     * @param string $field
     * @param bool $isAll
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getFieldById($id, $field = "*", $isAll = false) {
        $where = [];
        if (!$isAll) {
            $where[] = ["status", "<>", config('status.mysql.table_delete')];
        }
        $res = $this->where($where)->field($field)->find($id);
        return $res->toArray();
    }

}