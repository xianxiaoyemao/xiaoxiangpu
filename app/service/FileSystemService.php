<?php
declare (strict_types = 1);
namespace app\service;
use Monolog\Logger;
use Yansongda\Pay\Pay;
use Elasticsearch\ClientBuilder as ESClientBuilder;//这里必须带
class FileSystemService extends \think\Service
{
    /**
     * 注册服务
     *
     * @return mixed
     */
    public function register()
    {
    	//
        $this->app->bind('es', function () {
            // 从配置文件读取 Elasticsearch 服务器列表
            $builder = ESClientBuilder::create()->setHosts(config('database.elasticsearch.hosts'));
            return $builder->build();
        });
//        $this->app->bind('redis',\fast\Redis::class);
//        $this->app->bind('ses',\think\Session::class);
//        $this->app->bind('ses1',\app\api\controller\Index::class);
////        $this->app->bind('alipay', \Yansongda\Pay\Pay::class);
//        $this->app->bind('alipay', function () {
////            var_dump();die;
//            $config = config('pay.alipay');
//            // 调用 Yansongda\Pay 来创建一个支付宝支付对象
//            return Pay::alipay($config);
//        });
//        $this->app->bind('wechat_pay', function () {
//            $config = config('pay.wechat');
//            // 调用 Yansongda\Pay 来创建一个微信支付对象
//            return Pay::wechat($config);
//        });

    }

    /**
     * 执行服务
     *
     * @return mixed
     */
    public function boot()
    {
        //

    }
}
