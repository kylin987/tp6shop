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
 * 生成随机字符串
 * @param int $length
 * @return string
 */
function createNonceStr($length = 16) {
    $chars = "abcdefghjkmnpqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ23456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
}
