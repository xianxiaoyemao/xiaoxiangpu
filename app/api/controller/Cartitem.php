<?php
/**
 * Created by PhpStorm.
 * User: LENOVO
 * Date: 2020/11/17
 * Time: 21:38
 */

namespace app\api\controller;
use app\BaseController;
use app\common\model\Cart;
use app\Request;
class Cartitem extends BaseController{
    //购物车列表
    public function cartlist(Request $request){
        if (!$request->isPost()) return apiBack('fail', '请求方式错误', '10004');
        $uid = $request -> post('uid');
        $cartlist = (new Cart)::with(['product','skus'])->where(['user_id'=>$uid]) -> select() -> toArray();
        $arr =[];
        foreach ($cartlist as $key => $val){
            $arr['id'] = $val['id'];
            $arr['quantity'] = $val['quantity'];
            $arr['skuprice'] = $val['price'];
            $arr['product_id'] = $val['product_id'];
            $arr['sku_id'] = $val['sku_id'];
            $arr['name'] = $val['product']['name'];
            $arr['category_id'] = $val['product']['category_id'];
            $arr['images'] = $val['product']['images'];
            $arr['price'] = $val['product']['price'];
            $arr['discount_price'] = $val['product']['discount_price'];
            $arr['sales'] = $val['product']['sales'];
            $arr['is_rush'] = $val['product']['is_rush'];
            if($arr['is_rush'] == 1){
                $arr['secskill'] = ['skill_start'=>strtotime(C('skill_start')) - time(),'skill_end'=>strtotime(C('skill_end')) - time()];
            }
            $arr['product_spec_info'] = json_decode($val['product']['product_spec_info'],1);
            $arr['title'] = $val['skus']['title'];
            if($val['skus']['stock'] == 0){
                $arr['isstock'] = '已售完';//已售完
            }else{
                $arr['isstock'] = '在售中';
            }
        }
        return apiBack('success', '获取成功', '10000',$arr);
    }

    //添加商品到购物车
    public function cartsave(Request $request){
        if (!$request->isPost()) return apiBack('fail', '请求方式错误', '10004');
        $uid = $request -> post('uid');
        $product_id = $request -> post('product_id');
        $skuid = $request -> post('skuid');
        $quantity = $request -> post('quantity');
        $price = $request -> post('price');
        $cartinfo = (new Cart)::where(['user_id'=>$uid,'product_id'=>$product_id,'sku_id'=>$skuid]) ->find();
        if(empty($cartinfo)){
            $data =[
                'user_id' => (int)$uid,
                'product_id' => (int)$product_id,
                'sku_id' => (int)$skuid,
                'quantity' => (int)$quantity,
                'price' => floatval($price),
                'createtime' => time()
            ];
            $res =  (new Cart) -> save($data);
        }else{
            $quantity = $cartinfo -> quantity + (int)$quantity;
            $res =  (new Cart)::where(['id'=>$cartinfo->id])
                -> update(['quantity'=>$quantity]);
        }
        if($res){
            return apiBack('success', '操作成功', '10000');
        }else{
            return apiBack('fail', '操作失败', '10004');
        }
    }

    //删除购物车商品
    public function cartdel(Request $request){
        if (!$request->isPost()) return apiBack('fail', '请求方式错误', '10004');
        $cartid = $this ->post('ids');
        $res = (new Cart)->delete($cartid);
        if($res){
            return apiBack('success', '删除成功', '10000');
        }else{
            return apiBack('fail', '删除失败', '10004');
        }
    }
}
