<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {


    Route::get('/', [
        'uses'  =>  'IndexController@index',
        'as'     => 'index',
    ]);


    //用户登录
    Route::get('login', [
        'uses'  =>  'member\MemberController@getLogin',
        'as'     => 'login',
        'middleware' => ['guest'],
    ]);


    //用户登录处理
    Route::post('doLogin', [
        'uses'  =>  'member\MemberController@doLogin',
        'as'     => 'doLogin',
        'middleware' => ['guest'],
    ]);


    //用户注册视图显示
    Route::get('register', [
        'uses'  =>  'member\MemberController@getRegister',
        'as'     => 'register',
        'middleware' => ['guest'],
    ]);


    //验证码
    Route::get('service/create', [
        'uses'  =>  'service\ValidateCodeController@getCreate',
        'as'     => 'service.create',
        'middleware' => ['guest'],
    ]);


    //手机注册
    Route::post('doReg', [
        'uses'  => 'member\MemberController@doReg',
        'as'    => 'doReg',
        'middleware' => ['guest'],
    ]);


    //注册验证手机并发送短信
    Route::post('doPhone', [
        'uses'  => 'service\ValidateCodeController@doPhone',
        'as'    => 'doPhone',
        'middleware' => ['guest'],
    ]);


    //注册后验证邮箱
    Route::post('service/doEmail', [
        'uses'  => 'service\ValidateCodeController@doEmail',
        'as'    => 'service.doEmail',
        'middleware' => ['guest'],
    ]);


    //产品分类
    Route::get('category', [
        'uses'  => 'service\CategoryController@Category',
        'as'    => 'category',
        'middleware' => ['guest'],
    ]);


    //下级产品分类
    Route::get('category/parent_id/{parent_id}', [
        'uses'  => 'service\CategoryController@getCategoryByParentId',
        'middleware' => ['guest'],
    ]);


    //产品列表
    Route::get('product/category_id/{parent_id}',[
        'uses' => 'service\CategoryController@toProduct',
        'middleware'  => ['guest'],
    ]);


    //产品内容
    Route::get('product/{product_id}',[
        'uses' => 'service\CategoryController@toPdtContent',
        'middleware'  => ['guest'],
    ]);


    //添加到购物车
    Route::get('cart/add/{product_id}', [
        'uses'  => 'service\CartController@addCart',
        'middleware' => ['guest'],
    ]);



    //购物车结算
    Route::get('cart', [
        'uses'  => 'service\CartController@toCart',
        'middleware' => ['auth'],
    ]);


    //将购物车的商品删除
    Route::get('cart/delete', [
        'uses'  => 'service\CartController@deleteCart',
        'middleware' => ['auth'],
    ]);


    //结算中心
    Route::get('order/order_commit/{product_id}', [
        'uses'  => 'service\OrderController@orderCommit',
        'middleware' => ['auth'],
    ]);


    //结算中心
    Route::get('order/order_list', [
        'uses'  => 'service\CartController@orderList',
        'middleware' => ['auth'],
    ]);



    //结算中心
    Route::get('/pay/index', [
        'uses'  => 'service\PayController@index',
        'middleware' => ['auth'],
    ]);



    //结算中心
    Route::post('/pay/alipay', [
        'uses'  => 'service\PayController@alipay',
        'middleware' => ['auth'],
    ]);


    //结算中心
    Route::get('/pay/notify', [
        'uses'  => 'service\PayController@notify',
        'middleware' => ['auth'],
    ]);


    //结算中心
    Route::get('/pay/call_back', [
        'uses'  => 'service\PayController@call_back',
        'middleware' => ['auth'],
    ]);

});
