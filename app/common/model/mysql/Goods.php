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
}