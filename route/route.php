<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

//一般路由规则
//注册
Route::post('v1/register','api/v1.reg/register')
	->header('Access-Control-Allow-Origin','*')
	->header('Access-Control-Allow-Headers', 'Authorization, Content-Type, If-Match, If-Modified-Sinc
e, If-None-Match, If-Unmodified-Since, X-Requested-With, mobile, password, name, username, sex, address, idcard, email, education, school, major, practice, hobby, speciality, honor, type')
	->allowCrossDomain();
//学生
Route::get('v1/student/index','api/v1.student/index');
Route::get('v1/student/info','api/v1.student/info');
Route::put('v1/student/update','api/v1.student/update')->allowCrossDomain();
Route::put('v1/student/changeClass','api/v1.student/changeClass')->allowCrossDomain();
Route::get('v1/student/classInfo','api/v1.student/classInfo');
Route::get('v1/student/classScore','api/v1.student/classScore');

//班级
Route::get('v1/class/index','api/v1.clas/index');
Route::get('v1/class/info','api/v1.clas/info');
Route::put('v1/class/update','api/v1.clas/update')->allowCrossDomain();
//资源路由
// Route::resource(':version/user','api/:version.user')->except(['index', 'delete']);
// Route::resource(':version/student','api/:version.student')->except(['delete']);

//生成access_token
Route::post(':version/token','api/:version.token/token')
	->header('Access-Control-Allow-Origin','*')
	->header('Access-Control-Allow-Headers', 'Authorization, Content-Type, If-Match, If-Modified-Sinc
e, If-None-Match, If-Unmodified-Since, X-Requested-With, nonce, appid , mobile , passwd , nonce , timestamp , sign')
	->allowCrossDomain();

//测试路由
Route::get('index','index/index/index');

//所有路由匹配不到情况下触发该路由
Route::miss('\app\api\controller\Exception::miss');
