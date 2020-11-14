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



//随机32位字符串
    private function nonce_str(){
        $result = '';
        $str = 'QWERTYUIOPASDFGHJKLZXVBNMqwertyuioplkjhgfdsamnbvcxz';
        for ($i=0;$i<32;$i++){
            $result .= $str[rand(0,48)];
        }
        return $result;
    }


//生成订单号
    private function order_number($openid){
        //date('Ymd',time()).time().rand(10,99);//18位
        return md5($openid.time().rand(10,99));//32位
    }
//签名 $data要先排好顺序
    private function sign($data){
        $stringA = '';
        foreach ($data as $key=>$value){
            if(!$value) continue;
            if($stringA) $stringA .= '&'.$key."=".$value;
            else $stringA = $key."=".$value;
        }
        $wx_key = '';//申请支付后有给予一个商户账号和密码，登陆后自己设置的key
        $stringSignTemp = $stringA.'&key='.$wx_key;
        return strtoupper(md5($stringSignTemp));
    }


//curl请求
    public function http_request($url,$data = null,$headers=array())
    {
        $curl = curl_init();
        if( count($headers) >= 1 ){
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($curl, CURLOPT_URL, $url);


        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);


        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }


//获取xml
    private function xml($xml){
        $p = xml_parser_create();
        xml_parse_into_struct($p, $xml, $vals, $index);
        xml_parser_free($p);
        $data = "";
        foreach ($index as $key=>$value) {
            if($key == 'xml' || $key == 'XML') continue;
            $tag = $vals[$value[0]]['tag'];
            $value = $vals[$value[0]]['value'];
            $data[$tag] = $value;
        }
        return $data;
    }


}
