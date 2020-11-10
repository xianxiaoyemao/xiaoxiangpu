<?php
namespace app\common\model;


class Spec extends BaseModel
{
    // 设置字段信息
    protected $schema = [
        'id'         => 'int',
        'cate_id'     => 'int',
        'spec_name'  => 'string',
        'status'     => 'int',
        'createtime' => 'int',
        'updatetime' => 'int',
    ];

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
