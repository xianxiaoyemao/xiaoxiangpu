<?php


namespace app\common\model;


class SpecInfo extends BaseModel
{
    // 设置字段信息
    protected $schema = [
        'id'         => 'int',
        'spec_id'     => 'int',
        'spec_value'  => 'string',
        'status'     => 'int',
        'createtime' => 'int',
        'updatetime' => 'int',
    ];

    public function spec(){
        return $this->belongsTo(Spec::class, 'spec_id', 'id');
    }
}
