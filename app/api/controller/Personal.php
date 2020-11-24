<?php

namespace app\api\controller;

use app\BaseController;
use app\common\model\User;
use app\Request;
use think\App;

class Personal extends BaseController
{
    protected $userModel = null;
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->userModel = new User();
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
        return apiBack('success', '成功', '10000', $row);
    }
}
