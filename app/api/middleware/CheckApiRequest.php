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
        if ($request->pathinfo() !== 'wechat/wxlogin') {
            $accessToken = $request->post('access_token');
            $openId = $request->post('openId');
            if ($accessToken == '' || $openId == '') return apiBack('fail', 'Invalid Request', '10004');
            $token = decrypt($accessToken, 'XIAOXIANGPU');
            if (Cache::get('REQUESTTOKEN_' . $openId)) {
                $cacheToken = Cache::get('REQUESTTOKEN_' . $openId);
            } else {
                $cacheToken = User::where('openid', $openId)->value('token');
                Cache::set('REQUESTTOKEN_' . $openId, $cacheToken, 9000);
            }
            if ($token !== $cacheToken) return apiBack('fail', '非法请求！！！', '10004');
        }
        return $next($request);
    }
}
