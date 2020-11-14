<?php
use think\facade\Route;
//用户登录
Route::post('wechat/wxlogin', 'wechat/wxLogin');
//首页配置
Route::post('index/config', 'index/config');

Route::post('wechat/getwechatdata', 'wechat/getWechatData');
