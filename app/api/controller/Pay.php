<?php

namespace app\api\controller;
use app\common\lib\Curl;
use app\common\lib\Show;

class Pay extends ApiBase {

    public function index(){
        $appid = 'yz0dhpq1rc5y';
        $key = 'f6i5w3manco83mowab5tb7x07pbe0etd';
        $stitching_symbol = '@7!';
        $time = time();
        $data = [
            'pay_type'  => 'weixin',
            'body'      => "言致在线商品",
            'order_id'  => 'yz'.rand(100000,999999),
            'total_price'   => 1,
            'goods_id'   => 5623,
            'appid'     => $appid,
            'time'      => $time,
        ];

        $token_data = [$time,$appid,$key,$data['order_id'],$data['total_price']];
        $data['token'] = md5(implode($stitching_symbol,$token_data));
        $url = "http://pay.zzyanzhi.com/pay/unifiedOrder";
        $res = Curl::post($url,$data);

        $result = json_decode($res,true);
        if ($result){
            if ($result['status'] == 1 && $result['result']['code_url']){
                echo "<img src='".$result['result']['code_url']."'>";
            }
        }else {
            return Show::error("异常错误", '', $res);
        }
    }

    //查询订单支付情况
    public function getOrderRes(){
        $appid = 'yz0dhpq1rc5y';
        $key = 'f6i5w3manco83mowab5tb7x07pbe0etd';
        $stitching_symbol = '@7!';
        $time = time();

        $order_id  = 'yz667497';

        $data = [
            'pay_type'  => 'weixin',
            'order_id'   => $order_id,
            'appid'     => $appid,
            'time'      => $time,
            'query'     => 1,
        ];

        $token_data = [$time,$appid,$key,$order_id];
        $data['token'] = md5(implode($stitching_symbol,$token_data));
        $url = "http://pay.zzyanzhi.com/pay/getOrder";
        $res = Curl::post($url,$data);

        $result = json_decode($res,true);

        if ($result){
            if ($result['status'] == 1){
                return Show::success($result['result']);
            }else {
                return Show::error($result['message']);
            }
        }else {
            return Show::error("异常错误", '', $res);
        }
    }

}