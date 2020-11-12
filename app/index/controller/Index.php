<?php


namespace app\index\controller;


use app\BaseController;

class Index extends BaseController
{
    public function index ()
    {
        return $this->fetch();
    }

    public function about ()
    {
        return $this->fetch();
    }

    public function production ()
    {
        return $this->fetch();
    }
}
