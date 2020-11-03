<?php

namespace app\api\controller;


use app\Request;

class Index
{
    public function userRegister (Request $request)
    {
        if (!$request->isPost()) return apiBack('ok', '请求方式错误', '10004');

    }
}
