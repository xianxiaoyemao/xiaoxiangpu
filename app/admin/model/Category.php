<?php


namespace app\admin\model;


use app\common\model\BaseModel;

class Category extends BaseModel
{
    // 设置字段信息
    protected $schema = [
        'id'         => 'int',
        'cate_name'  => 'string',
        'status'     => 'int',
        'createtime' => 'int',
        'updatetime' => 'int',
    ];

    //获取属性
    public function getStatusAttr($value)
    {
        $status = [9 => 'deleted', 1 => 'normal'];
        return $status[$value];
    }
}
