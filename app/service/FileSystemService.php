<?php
declare (strict_types = 1);
namespace app\service;
use Monolog\Logger;
use Yansongda\Pay\Pay;
use Elasticsearch\ClientBuilder as ESClientBuilder;//这里必须带
class FileSystemService extends \think\Service{
    /**
     * 注册服务
     *
     * @return mixed
     */
    public function register(){
    	//
        $this->app->bind('es', function () {
            // 从配置文件读取 Elasticsearch 服务器列表
            $builder = ESClientBuilder::create()->setHosts(config('database.elasticsearch.hosts'));
            return $builder->build();
        });

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
