<?php
namespace app\api\controller;
use app\Request;
class Pay{
    //https://blog.csdn.net/supergao222/article/details/77844651
    //https://qq52o.me/1659.html
    public function index(){
//        $ses = app('redis') ;
//        $ses -> set('hhhh','1111111111');
//        $res = $ses -> get('hhhh');
        $data = [];
        $config = [[
            'app_id' => '',
            'ali_public_key' => '',
            'private_key' => '',
            'log' => [
                'file' => app()->getRuntimePath().'public/storage/paylogs/alipay.log',
            ],
        ]];
        $wetch = app('wechat_pay') ;
        dump($wetch);die;
        echo $res;
        die;
        $params = [
            'appid' => '小程序的appid',
            'mch_id' => '商户id',
            // 随机串，32字符以内
            'nonce_str' => (string) mt_rand(10000, 99999),
            // 商品名
            'body' => '鞋子',
            // 订单号，自定义，32字符以内。多次支付时如果重复的话，微信会返回“重复下单”
            'out_trade_no' => '20170823001' . time(),
            // 订单费用，单位：分
            'total_fee' => 1,
            'spbill_create_ip' => $_SERVER['REMOTE_ADDR'],
            // 支付成功后的回调地址，服务端不一定真得有这个地址
            'notify_url' => 'https://myserver.com/notify.php',
            'trade_type' => 'JSAPI',
            // 小程序传来的OpenID
            'openid' => $_GET['openid'],
        ];
//        $result = $wechat->miniapp($order);
    }
}
