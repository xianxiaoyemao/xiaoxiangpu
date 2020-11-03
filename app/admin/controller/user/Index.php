<?php

namespace app\admin\controller\user;

use app\common\controller\Backend;
use app\Request;

class Index extends Backend
{
    /**
     * 用户列表
     * @param Request $request
     */
    public function list (Request $request)
    {
        $this->success();
    }
}
