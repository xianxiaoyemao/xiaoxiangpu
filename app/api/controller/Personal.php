<?php

namespace app\api\controller;

use app\BaseController;
use app\common\model\Config;
use app\common\model\User;
use app\Request;
use fast\Http;
use think\App;
use think\facade\Session;

class Personal extends BaseController
{
    protected $userModel = null;

    protected $convert = 0;

    protected $appId = '';

    protected $appSecret = '';

    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->userModel = new User();
        $this->convert = Config::where('name', 'scoreConvert')->value('value');

        $this->appId = Config::where('name', 'miniprogram_app_id')->value('value');
        $this->appSecret = Config::where('name', 'miniprogram_app_secret')->value('value');
    }

    /**
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index (Request $request)
    {
        if (!$request->isPost()) return apiBack('fail', '请求方式错误', '10004');
        $uid = $request->post('uid');
        $row = $this->userModel->where('id', $uid)->field('nickname, avatar, money, score')->find()->toArray();
        if (!$row) return apiBack('fail', '暂无数据', '10004');
        $data = [
          'row' => $row,
          'convertValue' => $this->convert
        ];
        return apiBack('success', '成功', '10000', $data);
    }

    /**
     * 兑换现金
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function convert (Request $request)
    {
        if (!$request->isPost()) return apiBack('fail', '请求方式错误', '10004');
        $uid = $request->post('uid');
        $user = User::where('id', $uid)->find();
        if ($user->score < $this->convert) return apiBack('fail', '积分不够，快去签到下单赚积分吧', '10004');
        $user->money += intval($user->score / $this->convert);
        $user->score = $user->score % $this->convert;
        $user->save();
        return apiBack('success', '兑换成功', '10000');
    }

    /**
     * 邀请列表
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function invite (Request $request)
    {
        if (!$request->isPost()) return apiBack('fail', '请求方式错误', '10004');
        $uid = $request->post('uid');
        $data = User::where('invitecode', $uid)->field('nickname, createtime')->select()->toArray();
        return apiBack('success', '成功', '10000', $data);
    }

    /**
     * 用户分享二维码
     * @param Request $request
     * @return \think\response\Json
     */
    public function inviteCode (Request $request)
    {
        if (!$request->isPost()) return apiBack('fail', '请求方式错误', '10004');
        $uid = $request->post('uid');

        session_start();

        if(!\session('?mini_access_token') || !\session('?mini_expires_in') && time() > \session('mini_expires_in')) {
            $url = 'https://api.weixin.qq.com/cgi-bin/token';
            $param = [
                'grant_type' => 'client_credential',
                'appid' => $this->appId,
                'secret' => $this->appSecret
            ];
            $res = json_decode(Http::get($url, $param), 1);
            $access_token = $res['access_token'];
        } else {
            $access_token = \session('mini_access_token');
        }

        $qcode ='https://api.weixin.qq.com/cgi-bin/wxaapp/createwxaqrcode?access_token=' . $access_token;
        $param = json_encode(array("path"=>"pages/index/main?shareUid=" . $uid,"width"=> 150));

        $result = Http::post($qcode, $param);
        $base64_image ="data:image/jpeg;base64,".base64_encode($result);

        return apiBack('success', '成功', '10000', ['qrcode' => $base64_image]);
    }
}
