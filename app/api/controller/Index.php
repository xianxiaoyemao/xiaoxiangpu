<?php

namespace app\api\controller;


use app\common\model\Adv;
use app\Request;

class Index
{
    public function userRegister (Request $request)
    {
        if (!$request->isPost()) return apiBack('ok', '请求方式错误', '10004');

    }

    /**
     * 首页配置
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function config (Request $request)
    {
        if (!$request->isPost()) return apiBack('ok', '请求方式错误', '10004');

        //轮播图
        $swiper = Adv::where('title', '首页轮播')->field('advurl')->select()->toArray();
        $swiper = array_column($swiper, 'advurl');
        $data = [
            'swiper' => $swiper
        ];
        return apiBack('success', '成功', '10000', $data);
    }
}
