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

    public function prodeuctSku ()
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

//    public function toESArray(){
//        // 只取出需要的字段
//        $arr = Arr::only($this->toArray(), [
//            'id',
//            'product_core',
//            'title',
//            'long_title',
//            'bar_code',
//            'status',
//            'audit_status',
//            'rating',
//            'sold_count',
//            'review_count',
//            'price',
//            'image'
//        ]);
//
//        // 如果商品有类目，则 category 字段为类目名数组，否则为空字符串
//        $arr['category'] = $this->category ? explode(' - ', $this->category->full_name) : '';
//        // 类目的 path 字段
//        $arr['category_path'] = $this->category ? $this->category->path : '';
//        // strip_tags 函数可以将 html 标签去除
//        $arr['description'] = strip_tags($this->productdescriptions["description"]);
//        // 只取出需要的 SKU 字段
//        $arr['skus'] = $this->skus->map(function (ProductSku $sku) {
//            return Arr::only($sku->toArray(), ['title', 'description', 'price']);
//        });
//        $arr['shop_name'] = $this->shop->name;
//        // 只取出需要的商品属性字段
//        $arr['properties'] = $this->properties->map(function (ProductProperty $property) {
//            return Arr::only($property->toArray(), ['name', 'value']);
//        });
//
//        $arr['images'] = $this->images->map(function (ProductImage $productimage){
//            return Arr::only($productimage->toArray(),['image_url']);
//        });
//
//        return $arr;
//    }
}
