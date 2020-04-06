<?php

namespace app\api\controller\mall;

use app\api\controller\ApiBase;
use app\common\lib\Show;
use app\common\business\Goods;

class Lists extends ApiBase {
    public function index(){
        $pageSize = input("param.page_size", 10, "intval");
        $categoryId = input("param.category_id", 0, "intval");
        $field = input("param.field", "listorder", "trim");
        $order = input("param.order", 2, "intval");
        $order = $order == 2 ? "desc" : "asc";
        $order = [
            $field  => $order
        ];

        $keyword = input("param.keyword", "", "trim");
        $data = [];
        if (!empty($categoryId)){
            $data['category_path_id'] = $categoryId;
        }
        if (!empty($keyword)){
            $data['title']  = $keyword;
        }


        $goods = (new Goods())->getNormalLists($data, $pageSize, $order);


        return Show::success($goods);
    }
}
