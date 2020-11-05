<?php
/**
 * Created by PhpStorm.
 * User: LENOVO
 * Date: 2020/10/5
 * Time: 22:06
 */
namespace app\admin\controller\product;

use app\common\controller\Backend;
use app\common\model\Spec;
use app\Request;
use think\facade\Db;

class Goods extends Backend
{
    protected $categoryModel = null;

    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
        $this->categoryModel = new \app\admin\model\Category();
        //分类
        $cate = $this->categoryModel->where('status', 1)->order('createtime', 'desc')->column('cate_name', 'id');
        //属性
        $specInfo = Spec::with('specInfo')->visible(['specInfo' => ['spec_value', 'id']])->select()->toArray();
        foreach ($specInfo as $k => $v) {
            foreach ($v['specInfo'] as $key => $val) {
                $specInfo[$k]['info'][$val['id']] = $val['spec_value'];
            }
            unset($specInfo[$k]['specInfo']);
        }
        $this->assign('category', $cate);
        $this->assign('specInfo', $specInfo);
    }

    public function index ()
    {
        return $this-> fetch();
    }

    public function add(){
        if ($this->request->isPost()) {
            $params = $this->request->post('row/a');
            dump($params);die;
            if($params){
                $data['name'] = $params['name'];
                $data['category_id'] = $params['category_id'];
                $data['images'] = $params['images'];
                $data['price'] = $params['price'];
                $data['discount_price'] = $params['discount_price'];
                $data['sales'] = $params['sales'];
                $data['inventory'] = $params['inventory'];
                $data['introduce'] = $params['introduce'];
                $data['status'] = $params['status'] == 'normal' ? 1 : 2;
                $data['is_recommend'] = $params['is_recommend'] == 'normal' ? 1 : 2;
                $data['is_new'] = $params['is_new'] == 'normal' ? 1 : 2;
                $data['is_hot_sale'] = $params['is_hot_sale'] == 'normal' ? 1 : 2;

                $data['createtime'] = time();
                try {
                    validate('Admin.add')->check($data);
                } catch (\Exception $e) {
                    $this->error($e->getMessage());
                }
                $resultid = Db::name('admin')->insertGetId($data);
                $group['uid'] = $resultid;
                $group['group_id'] = $params['groupid'];
                Db::name('auth_group_access') -> insert($group);
                AdminLog::adminlog('添加管理员',$data);
                $this->success();
            }
            $this->error();
        }
        return $this-> fetch();
    }

    public function getSpec (Request $request)
    {
        if (!$request->isPost()) return apiBack('fail', 'Request错误', '10004');
        $cateId = $request->post('cate');
        $spec = new Spec();
        $row = $spec->where('cate_id', $cateId)->select()->toArray();
        return apiBack('success', '请求成功', '10000', $row);
    }
}
