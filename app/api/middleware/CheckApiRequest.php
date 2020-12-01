<?php

namespace app\api\middleware;

use app\BaseController;
use app\common\model\User;
use app\Request;
use think\facade\Cache;

class CheckApiRequest extends BaseController
{
    public function handle (Request $request, \Closure $next)
    {
        if ($request->pathinfo() !== 'wechat/wxlogin' || $request->pathinfo() !== 'payment/notify') {
            $accessToken = $request->post('access_token');
            $uid = $request->post('uid');
            if ($accessToken == '' || $uid == '') return apiBack('fail', 'Invalid Request', '10004');
            $token = decrypt($accessToken, 'XIAOXIANGPU');
            if (Cache::get('REQUESTTOKEN_' . $uid)) {
                $cacheToken = Cache::get('REQUESTTOKEN_' . $uid);
            } else {
                $cacheToken = User::where('id', $uid)->value('token');
                Cache::set('REQUESTTOKEN_' . $uid, $cacheToken, 9000);
            }
            if ($token !== $cacheToken) return apiBack('fail', '非法请求！！！', '10004');
        }
        return $next($request);
    }
}
