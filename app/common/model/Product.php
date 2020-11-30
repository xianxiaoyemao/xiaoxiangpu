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

    public function skus(){
        return $this->hasMany(ProductSku::class);
    }


    public function category(){
        return $this->belongsTo(Category::class) ;
    }

    public function shops(){
        return $this->belongsTo(Shops::class,'shop_id');
    }


    public function productcomment(){
        return $this->hasMany(ProductComment::class);
    }

    public function productdetails(){
        return $this -> hasOne(ProductDetails::class,'product_id');
    }

    public function cartitem(){
        return $this -> hasMany(Cart::class);
    }

    public function ordersdetail(){
        return $this -> hasOne(OrdersDetail::class);
    }

    public function productlist($where,$field,$orderby,$start,$end){
      $retust =  Product::where($where)
//          ::with(['skus'=>function($query){
//          $query -> field('id,title,price,product_id')  -> select() -> toArray()[0];
//      }])
          -> field($field)
          -> order($orderby)
          -> limit($start,$end)
          -> select() -> toArray();
      foreach ($retust as $key => $val){
          $retust[$key]['product_spec_info'] = json_decode($val['product_spec_info'],1);
      }
      return $retust;
    }
//    public function productdetails($productid){
//        $details = $this -> with(['shops'=>function(Query $query){
//            $query -> field('shop_id,title');
//        }])
//            -> fieldRaw('id,shop_id')
//            ->where('id',$productid)
////            -> field('id,name,images,price,discount_price,shop_id,sales,rating,review,introduce,product_spec_info,parea')
//            -> select() -> toArray();
//        return $details;
//    }

    //获取属性
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
