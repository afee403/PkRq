<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
/**
 * 全局接口
 */
Route::prefix('main')->group(function () {
    //获取token
    Route::get('/getToken', function (Request $request) {
        return var_dump($request);
    });
    //获取用户openid
    Route::post('/getOpenid', 'PublicController@getOpenid');
    //用户授权注册
    Route::post('/wxAuth', 'RUsersController@regster');
    // 图片上传
    Route::post('/uploadImg', 'PublicController@uploadImg');
    // 获取称号列表
    Route::get('/getHonorAll', 'PublicController@getHonorAll');
    // 获取勋章列表
    Route::get('/getMedalAll', 'PublicController@getMedalAll');
    // 获取系统通知
    Route::post('/getNotice', 'SystemController@getNotice');
    // 阅读通知
    Route::post('/readNotice', 'SystemController@readNotice');
    // 删除通知
    Route::post('/delNotice', 'SystemController@delNotice');
});

/**
 * 跑步相关
 */
Route::prefix('run')->group(function () {
    Route::get('/', function () {
        return "跑步接口暂未开发完成";
    });
    // 分享
    Route::post('/doShare', 'RunController@doShare');
});


/**
 * 夜奔列表
 */
Route::prefix('pub')->group(function () {
    Route::get('/', function () {
        return "活动接口暂未开发完成";
    });
    // 创建活动
    Route::post('/doActivity', 'ActivitysController@doActivity');
    // 删除活动
    Route::post('/delActivity', 'ActivitysController@delActivity');
    // 生成活动二维码
    Route::get('/getActivity', 'ActivitysController@getActivity');
    // 获取活动列表
    Route::post('/getList', 'ActivitysController@getList');
    // 获取轮播活动
    Route::get('/getSwipper', 'ActivitysController@getSwipper');
    // 获取轮播活动详细
    Route::get('/getDetail', 'ActivitysController@getDetail');
    // 创建展示
    Route::post('/doCourse', 'ActivitysController@doCourse');
    // 删除展示
    Route::post('/delCourse', 'ActivitysController@delCourse');
    // 获取展示列表
    Route::get('/getCourses', 'ActivitysController@getCourses');
    // 获取单个活动详细
    Route::get('/getCourseDetail', 'ActivitysController@getCourseDetail');
});

/**
 * 个人中心
 */
Route::prefix('user')->group(function () {
    Route::get('/', function () {
        return "token认证暂未开发完成";
    })->middleware('userAuth');
    //获取个人信息
    Route::post('/getUser', 'RUsersController@getUser');
    //获取个人信息（含勋章称号）
    Route::post('/getUserAll', 'RUsersController@getUserAll');
    //获取徽章
    Route::post('/lightMedal', 'RUsersController@lightMedal');
    //获取已获勋章
    Route::post('/getMedal', 'RUsersController@getMedal');
    //上传头像
    Route::post('/uploadImg', 'RUsersController@uploadImg');
    //修改个人信息
    Route::post('/doUpdate', 'RUsersController@doUpdate')->middleware('filterTime');
    //注销账号
    Route::post('/doUnset', 'RUsersController@doUnset');
});

/**
 * 管理接口
 */
Route::prefix('admin')->group(function () {
    Route::get('/', function () {
        return "管理接口暂未开发完成";
    });
    // 初始化数据
    Route::get('/initData', 'AdminController@initData');
    // 上传勋章图标
    Route::post('/uploadMedal', 'AdminController@uploadMedal');
    // 数据库调整，图片过渡
    Route::post('/transferImg', 'AdminController@transferImg');
});

/**
 * 定时任务测试
 */
Route::prefix('test')->group(function () {
    Route::get('/', function () {
        return "测试接口暂未开发完成";
    });
    // 月勋章授予
    Route::get('/grantMonthMedal', 'testController@grantMonthMedal');
    // 季勋章授予
    Route::get('/grantSeasonMedal', 'testController@grantSeasonMedal');
    // 月排行榜勋章授予
    Route::get('/grantRankingMedal', 'testController@grantRankingMedal');
    // 称号授予
    Route::get('/grantHonor', 'testController@grantHonor');
});
