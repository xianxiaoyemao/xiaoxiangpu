<?php

namespace app\api\controller;

use app\BaseController;
use app\common\controller\Backend;
use app\common\model\Config;
use app\common\model\User;
use app\Request;
use fast\Http;
use think\facade\Cache;
use think\facade\Db;

class Wechat extends BaseController
{
    protected $configModel = null;

    protected $userModel = null;

    protected $appId = '';

    protected $appSecret = '';

    public function initialize()
    {
        $this->configModel = new Config();
        $this->userModel = new User();
        //获取小程序配置
        $this->appId = $this->configModel->where('name', 'miniprogram_app_id')->value('value');
        $this->appSecret = $this->configModel->where('name', 'miniprogram_app_secret')->value('value');
    }

    /**
     * 小程序接口
     * @param Request $request
     * @return \think\response\Json
     */
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
        $res = json_decode(Http::get($url, $param), 1);
        $sessionKey = $res['session_key'];
        $openId = $res['openid'];


        $user = $this->userModel->where('openid', $openId)->find();
        if ($user) {
            $uid = $user-> id;
            $token = $user->token;
            $mobile = $user->mobile;
            $mobile == '' ? $isMobile = false : $isMobile = true;
        } else {
            $token = $openId . 'xiaoxiangpu';
            $this->userModel->token = $token;
            $this->userModel->openid = $openId;
            $this->userModel->save();
            $uid = $this->userModel-> id;
            $isMobile = false;
        }
        if (!Cache::get('REQUESTTOKEN_' . $uid)) {
            Cache::set('REQUESTTOKEN_' . $uid, $token, 9000);
        }
        $accessToken = encrypt($token, 'XIAOXIANGPU');

        $data = [
            'openId' => $openId,
            'uid' => $uid,
            'isMobile' => $isMobile,
            'token' => $accessToken,
            'sessionKey' => $sessionKey
        ];
        return apiBack('success', '请求成功', '10000', $data);
    }

    /**
     * 添加用户信息
     * @param Request $request
     * @return \think\response\Json
     */
    public function addUserInfo (Request $request)
    {
        if (!$request->isPost()) return apiBack('ok', '请求方式错误', '10004');
        $post = $request->post();
        $user = $this->userModel->where('id', $post['uid'])->find();
        $user->nickname = $post['nickname'];
        $user->avatar = $post['avatar'];
        $user->createtime = time();
        $user-> login_ip = get_client_ip();
        $user->save();
        return apiBack('success', '请求成功', '10000');
    }

    /**
     * 解密手机号
     * @param Request $request
     * @return \think\response\Json
     */
    public function getWechatData (Request $request)
    {
        if (!$request->isPost()) return apiBack('ok', '请求方式错误', '10004');

        $sessionKey = $request->post('session_key');
        $encryptedData = $request->post('encrypted_data');
        $iv = $request->post('iv');
        $shareUid = $request->post('shareUid');

        $data = $this->decryptData($encryptedData, $iv, $sessionKey);

        $mobile = $data['phoneNumber'];
        $uid = $request->post('uid');
        $user = $this->userModel->where('id', $uid)->find();
        //分享用户
        if ($shareUid) {
            $share_money = $this->configModel->where('name', 'share_money')->value('value');
            $user->invitecode = $shareUid;
            $shareUser = User::where('id', $shareUid)->find();
            $shareUser->money += $share_money;
            $shareUser->save();
        }
        $user->mobile = $mobile;
        $user->save();
        return apiBack('success', '请求成功', '10000');
    }
    /**
     * 检验数据的真实性，并且获取解密后的明文.
     * @param $encryptedData string 加密的用户数据
     * @param $iv string 与用户数据一同返回的初始向量
     * @param $data string 解密后的原文
     *
     * @return int 成功0，失败返回对应的错误码
     */
    private function decryptData( $encryptedData, $iv, $sessionKey)
    {
        if (strlen($sessionKey) != 24) {
            return -41001;
        }
        $aesKey=base64_decode($sessionKey);

        if (strlen($iv) != 24) {
            return -41002;
        }
        $aesIV=base64_decode($iv);

        $aesCipher=base64_decode($encryptedData);

        $result=openssl_decrypt( $aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);

        $dataObj=json_decode( $result, 1);

        if( $dataObj  == NULL )
        {
            return -41003;
        }
        if( $dataObj->watermark->appid != $this->appid )
        {
            return -41003;
        }
        return $dataObj;
    }
}
