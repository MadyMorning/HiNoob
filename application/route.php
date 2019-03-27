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
use think\Route;
// return [
//     '__pattern__' => [
//         'name' => '\w+',
//     ],
//     '[hello]'     => [
//         ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
//         ':name' => ['index/hello', ['method' => 'post']],
//     ],
//
// ];

Route::get('api/:version/banner/:id', 'api/:version.Banner/getBanner');   //获取Banner

Route::get('api/:version/theme', 'api/:version.Theme/getTheme');    //获取专题列表
Route::get('api/:version/theme/:id', 'api/:version.Theme/getThemeDetails');   //获取专题详情

Route::get('api/:version/product/recent', 'api/:version.Product/getRecentNewProducts'); //获取最近新品
Route::get('api/:version/product/:id', 'api/:version.Product/getProductDetails');   //获取商品详情

Route::get('api/:version/category', 'api/:version.Category/getCategoryList');   //获取分类列表
Route::get('api/:version/category/:id', 'api/:version.Category/getProduct');    //获取分类下商品

Route::get('api/:version/token/user', 'api/:version.Token/getToken');   //获取Token
Route::get('api/:version/token/verify', 'api/:version.Token/verifyToken');   //验证Token

Route::post('api/:version/address/create', 'api/:version.Address/createAddress');   //添加地址
Route::put('api/:version/address/update', 'api/:version.Address/updateAddress');   //更新地址

Route::post('api/:version/order/submit', 'api/:version.Order/submitOrders');   //提交订单

Route::post('api/:version/pay/payOrder', 'api/:version.Pay/getPreOrder');   //支付
