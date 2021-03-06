<?php
namespace app\common\lib;

/**
 * 对数组进行组装处理
 */
class Arr
{

    /**
     * 对数组进行树形组装
     * @param $data
     * @return array
     */
    public static function getTree($data){
        $items = [];
        foreach ($data as $k=>$v) {
            $v['category_id'] = $v['id'];
            unset($v['id']);
            $items[$v['category_id']] = $v;            
        }
        $tree = [];
        foreach ($items as $id => $item) {
            if (isset($items[$item['pid']])) {
                $items[$item['pid']]['list'][] = &$items[$id];
            }else {
                $tree[] = &$items[$id];
            }
        }
        return $tree;
    }

    /**
     * 从树形结构中取出对应数目的分叉
     * @param $data
     * @param int $firstCount
     * @param int $secondCount
     * @param int $threeCount
     * @return array
     */
    public static function sliceTreeArr($data, $firstCount = 5, $secondCount = 3, $threeCount = 5){
        //排序
        array_multisort(array_column($data,'listorder'), SORT_DESC, $data);
        $data = array_slice($data, 0, $firstCount);
        foreach ($data as $k => $v) {
            if (!empty($v['list'])) {
                //排序
                array_multisort(array_column($v['list'],'listorder'), SORT_DESC, $v['list']);
                $data[$k]['list'] = array_slice($v['list'], 0 , $secondCount);
                foreach ($data[$k]['list'] as $kk => $vv) {
                     if (!empty($vv['list'])) {
                         //排序
                         array_multisort(array_column($vv['list'],'listorder'), SORT_DESC, $vv['list']);
                         $data[$k]['list'][$kk]['list'] = array_slice($vv['list'], 0, $threeCount);
                     }
                 } 
            }
        }
        return $data;
    }

    /**
     * 分页默认返回的数据
     * @return array
     */
    public static function getPaginateDefaultData($num) {
        $result = [
            'total' => 0,
            'per_page'  => $num,
            'current_page'  => 1,
            'last_page' => 0,
            'data'  => [],
        ];
        return $result;
    }


    /**
     * 处理栏目上下级的显示
     * @param $tree
     * @param bool $isFirst
     * @param string $secondId
     * @param string $threeId
     * @return array
     */
    public static function doSearchCategoryList($tree, $isFirst = true, $secondId = "", $threeId = ""){
        $result = [
            'name'  => '',
            'focus_ids'=> [],
            'list'  => [],
        ];
        $result['name'] = $tree['name'];
        if (isset($tree['list'])){
            $tree['list'] = self::del_key($tree['list'], ['pid','path']);
            //排序
            array_multisort(array_column($tree['list'],'listorder'), SORT_DESC, $tree['list']);
            foreach ($tree['list'] as $k=>$v){
                $tree['list'][$k]['id'] = $v['category_id'];
                unset($tree['list'][$k]['category_id']);

                if ($isFirst){
                    if ($k == 0 && isset($v['list'])){
                        $three = $v['list'];
                    }
                }

                if ($secondId){
                    if ($v['category_id'] == $secondId && isset($v['list'])){
                        $three = $v['list'];
                        $result['focus_ids'][] = $v['category_id'];
                    }
                }

                if (isset($v['list'])){
                    unset($tree['list'][$k]['list']);
                }

            }
            $result['list'][] = $tree['list'];
            if (isset($three)){
                //排序
                array_multisort(array_column($three,'listorder'), SORT_DESC, $three);
                foreach ($three as $k=>$v){
                    $three[$k]['id'] = $v['category_id'];
                    unset($three[$k]['category_id']);
                    if (isset($three[$k]['list'])){
                        unset($three[$k]['list']);
                    }
                    if ($threeId){
                        if ($v['category_id'] == $threeId){
                            $result['focus_ids'][] = $v['category_id'];
                        }
                    }
                }
                $result['list'][] = $three;
            }
        }


        return $result;
    }

    /**
     * 利用递归删除数组中的某些key
     * @param $array
     * @param array $keys
     * @return mixed
     */
    public static function del_key($array, $keys = []){
        if (empty($array) || !is_array($array) || empty($keys) || !is_array($keys)){
            return $array;
        }

        foreach ($array as $k=>$v){
            foreach ($keys as $value){
                if (isset($v[$value])){
                    unset($v[$value]);
                }
            }

            if (isset($v['list'])){
                $v['list'] = self::del_key($v['list'], $keys);
            }
            $data[$k] = $v;
        }
        return $data;
    }
}