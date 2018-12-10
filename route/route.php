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
Route::post('v1/register','api/v1.reg/register')->allowCrossDomain();
//学生
Route::get('v1/student/index','api/v1.student/index')->allowCrossDomain();
Route::get('v1/student/info','api/v1.student/info')->allowCrossDomain();
Route::put('v1/student/update','api/v1.student/update')->allowCrossDomain();
Route::put('v1/student/changeClass','api/v1.student/changeClass')->allowCrossDomain();
Route::get('v1/student/classInfo','api/v1.student/classInfo')->allowCrossDomain();
Route::get('v1/student/classScore','api/v1.student/classScore')->allowCrossDomain();

Route::post('v1/student/getPortraitCallback','api/v1.student/getPortraitCallback');
Route::get('v1/student/getPortraitOss','api/v1.student/getPortraitOss');

//班级
Route::get('v1/class/index','api/v1.clas/index')->allowCrossDomain();
Route::get('v1/class/info','api/v1.clas/info')->allowCrossDomain();
Route::put('v1/class/update','api/v1.clas/update')->allowCrossDomain();

//生成access_token
Route::post(':version/token','api/:version.token/token')->allowCrossDomain();

//测试路由
Route::get('index','index/index/index');

//回调路由
Route::get('callback','index/index/callback');
//所有路由匹配不到情况下触发该路由
Route::miss('\app\api\controller\Exception::miss');
