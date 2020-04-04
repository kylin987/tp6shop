<?php

use think\facade\Route;


Route::post('smscode', 'sms/code','POST');
Route::resource('user', 'User');
Route::get('category/search/<id>','category/search');
Route::rule('subcategory/:id', 'category/sub');
