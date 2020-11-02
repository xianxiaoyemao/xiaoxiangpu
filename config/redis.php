<?php
/**
 * Created by PhpStorm.
 * User: LENOVO
 * Date: 2020/8/27
 * Time: 23:36
 */
use think\facade\Env;
return [
    // 默认缓存驱动
//    'default' => env('cache.driver', 'file'),
    'type' => 'redis',
    'host' => Env::get('redis.hostname', '127.0.0.1'),
    'port' => Env::get('redis.hostport', '6379'),
];