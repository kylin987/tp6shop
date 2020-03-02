<?php
// 应用公共文件

function show($status, $message, $data = [], $httpStatus = 200){
    $results = [
        'status'    => $status,
        'message'   => $message,
        'result'    => $data,
    ];

    return json($results, $httpStatus);
}


function kMd5($str,$salt) {
    return md5(md5($str)."_".$salt);
}



/**
 * 根据debug抛送异常
 * @param obj $e 异常信息
 * @param int $code
 * @param string $message 自定义提示信息 
 * @return obj
 */
function throwE($e,$code = 0,$message) {
    if (config('app.app_debug')) {
        throw new \think\Exception($e->getMessage(), $code);
    }else{
        throw new \think\Exception($message, $code);
    }
}


