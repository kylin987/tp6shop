<?php
namespace app\api\controller;

use app\common\lib\Show;
use app\common\business\Goods;


class Index extends ApiBase {


    public function getRotationChart() {
        $result = (new Goods())->getRotationChart();
        return Show::success($result);
    }

    /**
     * 获取首页产品推荐，api名字错了
     * @return \think\response\Json
     */
    public function cagegoryGoodsRecommend() {
        $categoryIds = [1,60];
        $result = (new Goods())->categoryGoodsRecommend($categoryIds);
        return Show::success($result);
    }
}
