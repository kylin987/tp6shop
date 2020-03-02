<?php

use think\facade\Route;


Route::post('smscode', 'sms/code','POST');
Route::resource('user', 'User');
