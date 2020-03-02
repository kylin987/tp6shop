<?php
namespace app\common\lib;
/**
 * 通知lib
 */
class Show
{
    /**
     * 成功api通知
     * @param  array  $data    [description]
     * @param  string $message [description]
     * @return [type]          [description]
     */
    public static function success($data = [], $message = "OK"){
        $results = [
            'status'    => config("status.success"),
            'message'   => $message,
            'result'    => $data,
        ];

        return json($results);
    }

    public static function error($data = [], $message = "OK", $status = 0){
        $results = [
            'status'    => $status,
            'message'   => $message,
            'result'    => $data,
        ];

        return json($results);
    }
}