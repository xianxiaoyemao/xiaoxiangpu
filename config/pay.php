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
    'wechat' => [
        'app_id' => '',
        'mch_id' => '',
        'key' => '',
        'cert_client' => '',
        'cert_key' => '',
        'log' => [
            'file' => app()->getRuntimePath().'public/storage/paylogs/wechat_pay.log',
        ],
    ],
];
