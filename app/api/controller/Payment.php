<?php
namespace app\api\controller;
use app\Request;
use think\facade\Config;
use think\facade\Log;
use Yansongda\Pay\Pay;

class Payment{

    protected $config = [
//      'app_id' => 'wxb3fxxxxxxxxxxx', // 公众号 APPID
        'miniapp_id' => '',
        'mch_id' => '',
        'key' => '',
        'notify_url' => '',
        'log' => [],
    ];

    public function __construct()
    {
        $this->config['miniapp_id'] = \config('pay.miniprogram.app_id');
        $this->config['mch_id'] = \config('pay.miniprogram.mch_id');
        $this->config['key'] = \config('pay.miniprogram.key');
        $this->config['log'] = \config('pay.miniprogram.log');
        $this->config['notify_url'] = 'https://mxxp.xianxiaoyemao.com/payment/notify';
    }

    //https://blog.csdn.net/supergao222/article/details/77844651
    //https://qq52o.me/1659.html
    public function pay ($order_no = '', $money = '', $msg = '', $openid = ''){
//        $ses = app('redis') ;
//        $ses -> set('hhhh','1111111111');
//        $res = $ses -> get('hhhh');

        $order = [
            'out_trade_no' => '1234567890',
            'total_fee' => floatval(0.01) * 100, // **单位：分**
            'body' => '测试',
            'openid' => 'oWGHA4svW6U3dk1CPkPCw7im3GEg',
        ];
        $pay = Pay::wechat($this->config);
        $result = $pay->miniapp($order);

        return $result;
    }



    public function notify()
    {
        $pay = Pay::wechat($this->config);

        try{
            $data = $pay->verify(); // 是的，验签就这么简单！
            Log::write('==============' . json_encode($data));
            Log::debug('Wechat notify', $data->all());
        } catch (\Exception $e) {
            // $e->getMessage();
        }

        return $pay->success()->send();// laravel 框架中请直接 `return $pay->success()`
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
