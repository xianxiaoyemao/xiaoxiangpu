<?php

namespace app\admin\model;
use app\common\model\BaseModel;
class Advposition extends BaseModel{
    public function advs()
    {
        return $this->hasMany(Adv::class);
    }
}
