<?php
namespace app\common\lib;

/**
 * 数字相关的业务逻辑
 */
class Filter
{
    
    public static function getFilter($request_query){
        $query = '';
        $query_array = explode("&",$request_query);
        if (count($query_array) > 1){
            unset($query_array[0]);
            foreach ($query_array as $v){
                if (!strstr($v, "page=") && !empty($v)){
                    $query .= "&".$v;
                }
            }
        }

        return $query;
    }
}