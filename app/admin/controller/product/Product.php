<?php
/**
 * Created by PhpStorm.
 * User: LENOVO
 * Date: 2020/10/5
 * Time: 22:06
 */
namespace app\admin\controller\product;
use app\common\controller\Backend;
use app\common\model\Product as ProductModel;
class Product extends Backend{



    public function index(){
        $ProductModel = new ProductModel();
        dump($ProductModel -> select());die;
        echo 111111;die;
    }
}