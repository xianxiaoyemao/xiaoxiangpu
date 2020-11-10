<?php
/**
 * Created by PhpStorm.
 * User: LENOVO
 * Date: 2020/10/5
 * Time: 22:02
 */
namespace app\common\model;
use think\Model;

class Product extends BaseModel
{
    //开启自动时间戳
    protected $autoWriteTimestamp=true;
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    //获取属性
    public function getStatusAttr($value)
    {
        $status = [9 => 'deleted', 1 => 'normal', 2 => 'hidden'];
        return $status[$value];
    }

    public function getIsHotSaleAttr($value)
    {
        $status = [1 => 'normal', 2 => 'hidden'];
        return $status[$value];
    }

    public function getIsRecommendAttr($value)
    {
        $status = [1 => 'normal', 2 => 'hidden'];
        return $status[$value];
    }

    public function getIsNewAttr($value)
    {
        $status = [1 => 'normal', 2 => 'hidden'];
        return $status[$value];
    }
}
