<?php

namespace app\admin\model;

use app\common\model\BaseModel;

class WebsiteComments extends BaseModel
{
    //获取属性
    public function getStatusAttr($value)
    {
        $status = [1 => 'normal', 2 => 'hidden'];
        return $status[$value];
    }
}
