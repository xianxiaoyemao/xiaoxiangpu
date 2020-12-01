<?php
namespace app\api\controller;
use app\Request;
use think\facade\Config;
use think\facade\Log;
use Yansongda\Pay\Pay;
use EasyWeChat\Factory;

class Payment{

//    protected $config = [
////      'app_id' => 'wxb3fxxxxxxxxxxx', // 公众号 APPID
//        'miniapp_id' => '',
//        'mch_id' => '',
//        'key' => '',
//        'notify_url' => '',
//        'log' => [],
//    ];

    protected $config = [
        // 必要配置
        'app_id'             => '',
        'mch_id'             => '',
        'key'                => '',   // API 密钥

            // 如需使用敏感接口（如退款、发送红包等）需要配置 API 证书路径(登录商户平台下载 API 证书)
        'cert_path'          => 'path/to/your/cert.pem', // XXX: 绝对路径！！！！
        'key_path'           => 'path/to/your/key',      // XXX: 绝对路径！！！！

        'notify_url'         => '',     // 你也可以在下单时单独设置来想覆盖它
    ];

    public function __construct()
    {
        $this->config['app_id'] = \config('pay.miniprogram.app_id');
        $this->config['mch_id'] = \config('pay.miniprogram.mch_id');
        $this->config['key'] = \config('pay.miniprogram.key');
        $this->config['log'] = \config('pay.miniprogram.log');
        $this->config['notify_url'] = 'https://mxxp.xianxiaoyemao.com/payment/notify';
    }


    public function pay ($order_no = '', $money = '', $msg = '', $openid = ''){
        /*$order = [
            'out_trade_no' => '1234567890',
            'total_fee' => floatval(0.01) * 100, // **单位：分**
            'body' => '测试',
            'openid' => 'oWGHA4svW6U3dk1CPkPCw7im3GEg',
        ];*/
        /*$pay = Pay::wechat($this->config);
        $result = $pay->miniapp($order);*/

        $app = Factory::payment($this->config);

        $result = $app->order->unify([
            'body' => '测试',
            'out_trade_no' => time() . '123460',
            'total_fee' => 0.01,
            'trade_type' => 'JSAPI', // 请对应换成你的支付方式对应的值类型
            'openid' => 'o-AqT4ljAzQV8yoUxfBv61gv-9y4',
        ]);
        $prepayId = $result['prepay_id'];
        $jssdk = $app->jssdk;
        $config = $jssdk->bridgeConfig($prepayId, false); // 返回数组
        return $config;
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
