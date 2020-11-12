<?php

namespace app\api\controller;

use app\common\controller\Backend;
use app\common\model\Config;
use app\Request;
use fast\Http;

class Wechat
{
    protected $configModel = null;

    protected $appId = '';

    protected $appSecret = '';

    public function initialize()
    {
        $this->configModel = new Config();
        //获取小程序配置
        $this->appId = $this->configModel->where('name', 'miniprogram_app_id')->value('value');
        $this->appSecret = $this->configModel->where('name', 'miniprogram_app_secret')->value('value');
    }

    public function wxLogin (Request $request)
    {
        if (!$request->isPost()) return apiBack('ok', '请求方式错误', '10004');
        $code = $request->post('code');
        $url = 'https://api.weixin.qq.com/sns/jscode2session';
        $param = [
            'appid' => $this->appId,
            'secret' => $this->appSecret,
            'js_code' => $code,
            'grant_type' => 'authorization_code'
        ];
        $res = Http::get($url, $param);
        dump($res);
    }
}
