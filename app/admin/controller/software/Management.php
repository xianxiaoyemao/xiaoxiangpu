<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/8/29
 * Time: 15:33
 */
namespace app\admin\controller\software;
use app\common\controller\Backend;
use think\facade\Db;
class Management extends Backend{
    protected $model = null;
    protected $noNeedRight = ['check', 'rulelist'];
    public function initialize(){
        parent::initialize();
        $this -> model = Db::name('app_version');
    }
    public function index(){
        if ($this->request->isAjax()) {
            [$where, $sort, $order, $offset, $limit] = $this->buildparams();
            $total = $this->model
                ->where($where)
                ->order($sort, $order)
                ->count();
//            $sort ='sort';$order='desc';
            $list = $this->model
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select() ->toArray();
//            echo $this -> model -> getLastSql();
            $result = ['total' => $total, 'rows' => $list];
            return json($result);
        }
        return $this -> fetch();
    }

    public function add(){
        if ($this->request->isPost()) {
            $params = $this->request->post('row/a');
            if($params){
                $params['createtime'] =time();
                $this -> model -> insert($params);
                $this->success();
            }
            $this->error();
        }
        return $this -> fetch();
    }
    public function edit($ids=null){
        $row = $this ->model ->find($ids);
        if (! $row) {
            $this->error(__('No Results were found'));
        }
        if ($this->request->isPost()) {
            $params = $this->request->post('row/a');
            if($params){
                $data['name'] = $params['name'];
                $data['title'] = $params['title'];
                $data['status'] = $params['status'];
                $data['sort'] = $params['sort'];
                $data['status'] = $params['status'];
                $data['updatetime'] = time();
                $this ->model -> where('id',$params['id']) ->  update($params);
                $this->success();
            }
            $this->error();

        }
        $this->assign('row', $row);
        return $this-> fetch();
    }
    /**
     * 删除.
     */
    public function del($ids = ''){
        if ($ids) {
            $this -> model -> where('id',$ids) -> delete();
            $this->success();
        }
        $this->error();
    }
    /**
     * 批量更新.
     *
     * @internal
     */
    public function multi($ids = '')
    {
        // 管理员禁止批量操作
        $this->error();
    }

    public function selectpage()
    {
        return parent::selectpage();
    }


    //上传软件
    public function upload_software(){
        $uplaodsrc = 'uploads/';
        $upload = new Exclfile("$uplaodsrc");
        $name = input('name');
        if($name == 'pathFile'){
            $url=$upload -> software('pathFile');
        }else if($name == 'uppathFile'){
            $url=$upload -> software('uppathFile');
        }
        exit(json_encode(array('status'=>1,'url'=>$url)));
    }
    //删除软件
    public function del_software(){
        $uplaodinputval = input('logo');
        $upload_file = __PUBLIC__ ;
        ds_unlink($upload_file,$uplaodinputval);
    }
    //上传文件
    public function apkfile()
    {
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('image');
//            var_dump($file);exit();
        // 移动到框架应用根目录/public/uploads/ 目录下
        if ($file) {
            $info = $file->move('uploads', 'H5F9DCF7B');
            if ($info) {
                $name = $info->getFilename();
                return $this->error('文件上传成功,重命名为' . $name);
            } else {
                // 上传失败获取错误信息
                echo $file->getError();
            }
        }
        return $this->fetch();
    }
    //删除文件
    public function unmdir(){
        $dir = __PUBLIC__."/uploads/msg/";

        del_dir($dir);
    }

}