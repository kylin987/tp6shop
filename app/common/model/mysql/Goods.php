<?php

namespace app\common\model\mysql;


/**
 * 商品model
 */
class Goods extends BaseModel
{

    public function getLists($where, $num, $likeKeys = []) {
        if (!empty($likeKeys)){
            $res = $this->withSearch($likeKeys, $where);
        }else {
            $res = $this;
        }
        $order = [
            "listorder" => "desc",
            "id"        => "desc",
        ];
        $result = $res->where("status", "<>", config('status.mysql.table_delete'))
            ->order($order)
            ->paginate($num);
        return $result;
    }

    //标题搜索器
    public function searchTitleAttr($query,$value){
        $query->where('title','like', '%' .$value . '%');
    }
    //发布日期搜索器
    public function searchCreateTimeAttr($query,$value){
        $query->whereBetweenTime('create_time',$value[0], $value[1]);
    }

    /**
     * 获取首页推荐大图
     * @param $data
     * @param string $field
     * @param int $limit
     * @return \think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getRotationChart($data,$field = "*",$limit = 5) {
        $order = [
            "listorder" => "desc",
            "id"        => "desc",
        ];
        $res = $this->where('big_image','<>','')
            ->where('status',config('status.mysql.table_normal'))
            ->where($data)
            ->field($field)
            ->order($order)
            ->limit($limit)
            ->select();
        return $res;
    }

    public function getImageAttr($value) {
        if (!strstr($value, 'http')){
            return request()->domain().$value;
        }
        return $value;
    }

    public function getNormalGoodsFindInSetCategoryId($categoryId, $field = true, $limit = 10){
        $order = [
            "listorder" => "desc",
            "id"        => "desc",
        ];

        $res = $this->whereFindInSet("category_path_id", $categoryId)
            ->where("status", "=", config("status.mysql.table_normal"))
            ->field($field)
            ->order($order)
            ->limit($limit)
            ->select();
        //echo $this->getLastSql();exit;
        return $res;
    }

}