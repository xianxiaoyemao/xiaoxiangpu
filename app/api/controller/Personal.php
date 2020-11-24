<?php

namespace app\api\controller;

use app\BaseController;
use app\common\model\Config;
use app\common\model\User;
use app\Request;
use think\App;

class Personal extends BaseController
{
    protected $userModel = null;

    protected $convert = 0;

    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->userModel = new User();
        $this->convert = Config::where('name', 'scoreConvert')->value('value');
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
        $score = $request->post('score');
        $uid = $request->post('uid');
        $user = User::where('id', $uid)->find();
        if ($user->score < $this->convert) return apiBack('fail', '积分不够，快去签到下单赚积分吧', '10004');
        $user->money += intval($user->score / $this->convert);
        $user->score = $score % $this->convert;
        $user->save();
        return apiBack('success', '兑换成功', '10000');
    }
}
