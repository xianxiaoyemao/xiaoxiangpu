<?php
/**
 * Created by PhpStorm.
 * User: LENOVO
 * Date: 2020/10/5
 * Time: 22:02
 */
namespace app\common\model;
use think\Model;
use app\admin\model\Category;
class Product extends BaseModel
{
    //开启自动时间戳
    protected $autoWriteTimestamp=true;
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    public function skus ()
    {
        return $this->hasMany(ProductSku::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function shops(){
        return $this->belongsTo(Shops::class,'shop_id');
    }


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

//    const TYPE_NORMAL ='product';
//
//    const TYPE_SECKILL = 'seckill';
//
//    public static $typeMap = [
//        self::TYPE_SECKILL => '秒杀商品',
//    ];
//
//    public function seckill()
//    {
//        return $this->hasOne(SeckillProduct::class);
//    }
//
//    protected $casts = [
//        'status' => 'boolean', // on_sale 是一个布尔类型的字段
//    ];

    public function toEsarray(){
        // 只取出需要的字段
        $arr = $this -> visible([
            'id',
            'title',
            'bar_code',
            'images',
            'price',
            'sales',
            'introduce',
            'rating',
            'review',
            'status',
            'category_id',
            'shop_id'
        ]) -> toArray();
        $arr['category'] = Category::find($arr['category_id'])['cate_name'];
        $arr['shop_name'] = Shops::find($arr['shop_id'])['title'];
        return $arr;
    }
}
