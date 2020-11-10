<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/8/10
 * Time: 16:10
 */
namespace app\admin\controller\auth;
use fast\Tree;
use app\admin\model\AuthRule;
use app\admin\model\AuthGroup;
use app\common\controller\Backend;
use app\admin\model\AuthGroupAccess;
use think\facade\Config;
use think\facade\Db;
use think\Request;
use app\admin\model\AdminLog as Admin_Log;
class Adminlog extends Backend{

    protected $childrenGroupIds = [];
    protected $childrenAdminIds = [];
    protected $adminlog = null;
    public function initialize(){
        parent::initialize();
        $this -> adminlog = new Admin_Log();
        $this->childrenAdminIds = $this->auth->getChildrenAdminIds(true);
        $this->childrenGroupIds = $this->auth->getChildrenGroupIds($this->auth->isSuperAdmin() ? true : false);
        $groupName = AuthGroup::where('id', 'in', $this->childrenGroupIds)
            ->column('id,name');
        $this->assign('groupdata', $groupName);

    }
    public function index(){
        if ($this->request->isAjax()) {
            [$where, $sort, $order, $offset, $limit] = $this->buildparams();
            $total = $this -> adminlog
                ->where($where)
                ->where('admin_id', 'in', $this->childrenAdminIds)
                ->order($sort, $order)
                ->count();

            $list = $this -> adminlog
                ->where($where)
                ->where('admin_id', 'in', $this->childrenAdminIds)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            $result = ['total' => $total, 'rows' => $list];
            return json($result);
        }
        return $this -> fetch();
    }
    /**
     * 详情.
     */
    public function detail($ids){
        $row= $this -> adminlog-> where('id',$ids) -> find();
        if (! $row) {
            $this->error(__('No Results were found'));
        }
        $row['createtime'] = date('Y-m-d H:i:s',$row['createtime']);
        $this->assign('row', $row);
        return $this->fetch();
    }
    /**
     * 删除.
     */
    public function del($ids = ''){
        if ($ids) {
            $childrenGroupIds = $this->childrenGroupIds;
            $adminList = $this -> adminlog-> where('id', 'in', $ids)->where('admin_id', 'in',
                function ($query) use ($childrenGroupIds) {
                    $query->name('auth_group_access')->field('uid');
                })->select();

            if ($adminList) {
                $deleteIds = [];
                foreach ($adminList as $k => $v) {

                    $deleteIds[] = $v['id'];
                }
                if ($deleteIds) {
                    $this -> adminlog-> delete($deleteIds);
                    $this->success();
                }
            }
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
}
