<?php
/**
 * Created by PhpStorm.
 * User: LENOVO
 * Date: 2020/10/5
 * Time: 22:02
 */
namespace app\common\model;
use think\Model;

class Product extends Model{
    protected $connection = 'mysql_products';

    protected $fillable = [
        'product_core', 'title', 'category_id', 'status',
        'audit_status', 'shop_id', 'description_id', 'rating',
        'sold_count','review_count','price','image'
    ];
}