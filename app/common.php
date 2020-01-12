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