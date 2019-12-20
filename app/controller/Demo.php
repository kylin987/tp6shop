<?php
namespace app\controller;

use app\BaseController;

class Demo extends BaseController
{    

    public function hello()
    {
    	$result = [
    		'status'	=>1,
    		'message'	=>"ok",
    		'result'	=>[
    			'id'	=>1,
    		],
    	];
        return json($result);
    }
}
