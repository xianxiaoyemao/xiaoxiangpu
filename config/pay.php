<?php
return [
    'alipay' => [
        'app_id' => '',
        'ali_public_key' => '',
        'private_key' => '',
        'log' => [
            'file' => app()->getRuntimePath().'public/storage/paylogs/alipay.log',
        ],
    ],
    'miniprogram' => [
        'app_id' => 'wx2321f85bc3478c47',
        'mch_id' => '1604563313',
        'key' => 'xianxiaoyemao1234567890XIAOYEMAO',
        'log' => [
            'file' => app()->getRuntimePath().'public/storage/paylogs/mini_pay.log',
        ],
    ],
];
