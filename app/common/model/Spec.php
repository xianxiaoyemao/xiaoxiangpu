<?php

namespace app\common\model;


class Spec extends BaseModel
{
    public function specInfo()
    {
        return $this->hasMany(SpecInfo::class);
    }

    //获取属性
    public function getStatusAttr($value)
    {
        $status = [9 => 'deleted', 1 => 'normal'];
        return $status[$value];
    }
}
