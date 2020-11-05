<?php

namespace app\common\model;


class SpecInfo extends BaseModel
{

    //获取属性
    public function getStatusAttr($value)
    {
        $status = [9 => 'deleted', 1 => 'normal'];
        return $status[$value];
    }
}
