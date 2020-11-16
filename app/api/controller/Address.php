<?php


namespace app\api\controller;

use app\BaseController;
use app\Request;
use app\common\model\Area;
use app\common\model\Address as AdressModel;
class Address extends BaseController
{
    //地址管理列表
    public function addresslist(Request $request){
        if (!$request->isPost()) return apiBack('fail', '请求方式错误', '10004');
        $uid = $request -> post('uid');
        $address = (new AdressModel)::where(['status'=>1,'user_id'=>$uid])
            -> field('id,contact_name as username,contact_phone as phone,disarea,address,is_defult')
            -> order('createtime desc')
            -> select() -> toArray();
        $isdeful = (new AdressModel)::where(['status'=>1,'user_id'=>$uid,'is_defult'=>1]) -> value('id');
        $data['is_defult'] = $isdeful;
        $data['data'] = $address;
        return apiBack('success', '获取地址列表成功', '10000',$data);
    }

    //添加地址
    public function addresssave(Request $request){
        if (!$request->isPost()) return apiBack('fail', '请求方式错误', '10004');
        $address = new AdressModel();
        $uid = $request -> post('uid');
        $addresscount = count($address -> where(['is_defult'=>1,'user_id'=>$uid]) -> value('id'));
        if($addresscount < 1){
            $data['is_defult'] = 1;
        }
        $data['contact_name'] = $request -> post('username');
        $data['contact_phone'] = $request -> post('phone');
        $data['user_id'] = $uid;
        $data['disarea'] = $request -> post('disarea');
        $data['address'] = $request -> post('address');
        $data['status'] = 1;
        $data['createtime'] = time();
        $res = $address -> save($data);
        if($res){
            return apiBack('success', '添加地址成功', '10000');
        }else{
            return apiBack('fail', '添加地址失败', '10004');
        }
    }

    //获取单个地址
    public function addressone(Request $request){
        if (!$request->isPost()) return apiBack('fail', '请求方式错误', '10004');
        $address = new AdressModel();
        $id = $request -> post('id');
        $addressinfo = $address -> where(['id'=>$id])
            -> field('id,contact_name as username,contact_phone as phone,disarea,address,is_defult')
            -> find();
        return apiBack('success', '获取地址信息成功', '10000',$addressinfo);
    }
    //编辑地址
    public function addressupdate(Request $request){
        if (!$request->isPost()) return apiBack('fail', '请求方式错误', '10004');
        $address = new AdressModel();
        $uid = $request -> post('uid');
        $id = $request -> post('id');
        $data['contact_name'] = $request -> post('username');
        $data['contact_phone'] = $request -> post('phone');
        $data['user_id'] = $uid;
        $data['disarea'] = $request -> post('disarea');
        $data['address'] = $request -> post('address');
        $data['status'] = 1;
        $data['updatetime'] = time();
        $res = $address -> where('id',$id) ->  save($data);
        if($res){
            return apiBack('success', '更新地址成功', '10000');
        }else{
            return apiBack('fail', '更新地址失败', '10004');
        }
    }

    //设置默认地址
    public function addressisdeful(Request $request){
        $address = new AdressModel();
        $uid = $request -> post('uid');
        $id = $request -> post('id');
        $res = $address -> where('id',$id) -> update(['is_defult'=>1]);
        if($res){
            $address -> where("user_id = $uid and id != $id") ->  update(['is_defult'=>0]);
        }
        return apiBack('success', '设置默认地址成功', '10000');
    }

    //删除地址
    public function addressdel(Request $request){
        if (!$request->isPost()) return apiBack('fail', '请求方式错误', '10004');
        $address = new AdressModel();
        $uid = $request -> post('uid');
        $id = $request -> post('id');
        $address -> where('id',$id) ->  delete();
        return apiBack('success', '删除成功', '10000');
    }
    //获取地区一级
    public function getarea($pcode){
        $data = (new Area) -> where('parent_region_code',$pcode) -> select()->toArray();
        return apiBack('success', '成功', '10000', $data);
    }

    public function getaraechid(Request $request){}
}
