<?php
namespace app\api\controller;
use app\common\model\Orders;
use app\common\model\OrdersPay;
use app\Request;
use think\facade\Config;
use think\facade\Db;
use think\facade\Log;
use Yansongda\Pay\Pay;
use EasyWeChat\Factory;

class Payment{

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

    /**
     * 支付
     * @param string $order_no
     * @param string $money
     * @param string $msg
     * @param string $openid
     * @return array|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function pay ($order_no = '', $money = '', $msg = '', $openid = ''){
        $app = Factory::payment($this->config);

        $result = $app->order->unify([
            'body' => $msg,
            'out_trade_no' => $order_no,
            'total_fee' => $money * 100,
            'trade_type' => 'JSAPI', // 请对应换成你的支付方式对应的值类型
            'openid' => $openid,
        ]);
        if ($result['return_code'] != 'SUCCESS' || $result['result_code'] != 'SUCCESS') {
            return [];
        } else {
            $prepayId = $result['prepay_id'];
            $jssdk = $app->jssdk;
            $config = $jssdk->bridgeConfig($prepayId, false); // 返回数组
            return $config;
        }
    }

    /**
     * 回调地址
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \EasyWeChat\Kernel\Exceptions\Exception
     */
    public function notify()
    {
        $app = Factory::payment($this->config);
        $response = $app->handlePaidNotify(function($message, $fail){
            $order_id = OrdersPay::where('order_sn', $message['out_trade_no'])->value('order_ids');
            $order = explode(',', $order_id);
            Db::name('orders')->where('id', 'in', $order)->update(['status' => 2, 'paytime' => time()]);
            if (!$order_id) { // 如果订单不存在 或者 订单已经支付过了
                return true;
            }

            if ($message['return_code'] === 'SUCCESS') { // return_code 表示通信状态，不代表支付状态
                // 用户是否支付成功
                if ($message['result_code'] === 'SUCCESS') {
                } elseif ($message['result_code'] === 'FAIL') {
                    return true;
                }
            } else {
                return false;
            }
        });
        return $response;
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
