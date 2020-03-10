<?php
namespace app\api\controller;

use app\common\lib\Show;
use app\common\business\Goods;
use think\App;


class Index extends ApiBase {


    public function getRotationChart() {
        $result = (new Goods())->getRotationChart();
        return Show::success($result);
    }
}
