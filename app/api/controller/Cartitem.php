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
use app\common\model\ProductSku;
use app\Request;
class Cartitem extends BaseController{
    //购物车列表
    public function cartlist(Request $request){
        if (!$request->isPost()) return apiBack('fail', '请求方式错误', '10004');
        $uid = $request -> post('uid');
        $category_id = $request -> post('cid') ?? 0;
        if($category_id == 0){
            $cartlist = (new Cart)  -> with(['product','skus'])->where(['user_id'=>$uid]) -> select() -> toArray();
        }else{
            $haswhere['category_id'] = $category_id;
            $cartlist = (new Cart) -> hasWhere('product', $haswhere) -> with(['product','skus'])->where(['user_id'=>$uid]) -> select() -> toArray();
        }
        $arr =[];
        foreach ($cartlist as $key => $val){
            $arr[$key]['id'] = $val['id'];
            $arr[$key]['quantity'] = $val['quantity'];
            $arr[$key]['total_price'] = $val['price'] * $val['quantity'];
            $arr[$key]['skuprice'] = $val['price'];
            $arr[$key]['product_id'] = $val['product_id'];
            $arr[$key]['sku_id'] = $val['sku_id'];
            $arr[$key]['specvalue'] = $val['specvalue'];
            $arr[$key]['name'] = $val['product']['name'];
            $arr[$key]['category_id'] = $val['product']['category_id'];
            $arr[$key]['images'] = $val['product']['images'];
            $arr[$key]['price'] = $val['product']['price'];
            $arr[$key]['discount_price'] = $val['product']['discount_price'];
            $arr[$key]['sales'] = $val['product']['sales'];
            $arr[$key]['is_rush'] = $val['product']['is_rush'];
            if($val['product']['is_rush'] == 1){
                $arr[$key]['secskill'] = ['skill_start'=>strtotime(C('skill_start')) - time(),'skill_end'=>strtotime(C('skill_end')) - time()];
            }
            $arr[$key]['product_spec_info'] = json_decode($val['product']['product_spec_info'],1);
            $arr[$key]['title'] = $val['skus']['title'];
            if($val['skus']['stock'] == 0){
                $arr[$key]['isstock'] = '已售完';//已售完
            }else{
                $arr[$key]['isstock'] = '在售中';
            }
        }
        return apiBack('success', '获取成功', '10000',$arr);
    }

    //添加商品到购物车
    public function cartsave(Request $request){
        if (!$request->isPost()) return apiBack('fail', '请求方式错误', '10004');
        $uid = $request -> post('uid');
        $type = $request -> post('type');
        $pid = $request -> post('product_id');
        $skuid = $request -> post('skuid');
        $price = $request -> post('price');
        $specvalue = $request -> post('specvalue');
        $quantity = $request -> post('quantity') ?? 1;
        switch ($type){
            case 'list':
                //查询商品
                $pskulist = (new ProductSku)::where('product_id',$pid) -> field('id as skuid,price,stock') -> select() -> toArray();
                $data = [
                    'user_id' => (int)$uid,
                    'product_id' => (int)$pid,
                    'sku_id' => (int)$pskulist[0]['skuid'],
                    'quantity' => (int)$quantity,
                    'specvalue'=> '',
                    'price' => floatval($pskulist[0]['price']),
                    'createtime' => time()
                ];
                $stock = $pskulist[0]['stock'];
                break;
            case 'details':
                //查询
                $skunum = (new ProductSku)::where('id',$skuid) -> field('stock') -> find() -> toArray();
                $data = [
                    'user_id' => (int)$uid,
                    'product_id' => (int)$pid,
                    'sku_id' => (int)$skuid,
                    'quantity' => (int)$quantity,
                    'specvalue'=> $specvalue,
                    'price' => floatval($price),
                    'createtime' => time()
                ];
                $stock = $skunum['stock'];
                break;
        }
        $cartinfo = (new Cart)::where(['user_id'=>$uid,'product_id'=>$pid,'sku_id'=>$data['sku_id'],'specvalue'=>$data['specvalue']]) ->find();
        if(empty($cartinfo)){
            $res =  (new Cart) -> save($data);
        }else{
            $quantity = $cartinfo -> quantity + (int)$quantity;
            $data = [
                'quantity' => $quantity,
            ];
            $res =  (new Cart)::where(['id'=>$cartinfo->id])
                -> update($data);
        }
        if($res){
            $res=app('redis')->llen('goods_store'.$pid.$data['sku_id']);
            $count=$stock - $res;
            for($i=0;$i<$count;$i++){
                app('redis') ->lpush('goods_store'.$pid.$data['sku_id'],1);
            }
            return apiBack('success', '添加成功', '10000');
        }else{
            return apiBack('fail', '添加失败', '10004');
        }
    }

    //删除购物车商品
    public function cartdel(Request $request){
        if (!$request->isPost()) return apiBack('fail', '请求方式错误', '10004');
        $cartid = $request ->post('ids');
        $where = 'id in('.$cartid.')';
        $res = (new Cart)-> where($where)->delete();
        if($res){
            return apiBack('success', '删除成功', '10000');
        }else{
            return apiBack('fail', '删除失败', '10004');
        }
    }
}
