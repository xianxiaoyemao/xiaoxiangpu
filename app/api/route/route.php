<?php
use think\facade\Route;
//用户登录
Route::post('wechat/wxlogin', 'wechat/wxLogin');
//首页配置
Route::post('index/config', 'index/config');

Route::post('wechat/getwechatdata', 'wechat/getWechatData');
Route::post('wechat/addUserInfo', 'wechat/addUserInfo');

Route::post('index/sign', 'index/sign');

//地址管理接口
Route::post('address/addresslist', 'address/addresslist');
Route::post('address/getarea/:pcode', 'address/getarea');
Route::post('address/addressone', 'address/addressone');//获取单个地址
Route::post('address/addresssave', 'address/addresssave');
Route::post('address/addressupdate', 'address/addressupdate');//编辑地址
Route::post('address/addressdel', 'address/addressdel');//删除地址
Route::post('address/addressisdeful', 'address/addressisdeful');//设置默认地址
//地址管理接口


//产品管理
Route::post('product/productlist', 'product/productlist');
Route::post('product/productdetails', 'product/productdetails');//商品详情

//购物车管理
Route::post('cartitem/cartlist', 'cartitem/cartlist');
Route::post('cartitem/cartsave', 'cartitem/cartsave');
Route::post('cartitem/cartdel', 'cartitem/cartdel');
Route::post('cartitem/updateCart', 'cartitem/updateCart');//更改购物车信息

Route::post('cartitem/cart2', 'cartitem/cart2');
Route::post('cartitem/cartsubmit', 'cartitem/cartsubmit');//提交订单
//Route::post('cartitem/cartconfirm', 'cartitem/cartconfirm');//确认订单
//订单管理
Route::post('order/orderlist', 'order/orderlist');
Route::post('order/orderdetail', 'order/orderdetail');
//我的
Route::post('personal/index', 'personal/index');
Route::post('personal/convert', 'personal/convert');
