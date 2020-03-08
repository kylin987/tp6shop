<?php

namespace app\common\model\mysql;

/**
 * 
 */
class Category extends BaseModel
{

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

        $order = [
            "listorder" => "desc",
            "id"        => "asc",
        ];

        return $this->where($where)->field($field)->order($order)->select();
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


    public function getNormalByPid($pid, $field = "id, name, pid") {
        $where = [
            "pid"   => $pid,
            "status" => config('status.mysql.table_normal'),
        ];
        $order = [
            "listorder" => "desc",
            "id"        => "asc",
        ];

        $res = $this->where($where)
            ->field($field)
            ->order($order)
            ->select();
        //halt($this->getLastSql());
        return $res;
    }

}