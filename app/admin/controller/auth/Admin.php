<?php
/**
 * Created by PhpStorm.
 * User: LENOVO
 * Date: 2020/7/16
 * Time: 21:06
 */
namespace app\admin\controller\auth;
use fast\Tree;
use fast\Random;
use app\admin\model\AuthGroup;
use app\admin\model\AuthGroupAccess;
use app\common\controller\Backend;
use think\Facade\DB;
use app\admin\model\AdminLog;
class Admin extends Backend{
    protected $model = null;
    protected $groupdata = null;
    protected $childrenGroupIds = [];
    protected $childrenAdminIds = [];
    public function initialize(){
        parent::initialize();
        $this->model = new \app\admin\model\Admin();
        $this->childrenAdminIds = $this->auth->getChildrenAdminIds(true);
        $this->childrenGroupIds = $this->auth->getChildrenGroupIds(true);
        $groupList = AuthGroup::where('id', 'in', $this->childrenGroupIds)->select()->toArray();
//        $groupList = AuthGroup::where('status', 'normal')->select()->toArray();
        Tree::instance()->init($groupList);
        $groupdata = [];
        //判断是否为超级管理员
        if ($this->auth->isSuperAdmin()) {
            $result = Tree::instance()->getTreeList(Tree::instance()->getTreeArray(0));
            foreach ($result as $k => $v) {
                $groupdata[$v['id']] = $v['name'];
            }
        } else {
//            $this->groupdata = Tree::instance()->getTreeList(Tree::instance()->getTreeArray(0), 'name');
//            $ruledata = [0 => __('None')];
//            foreach ($this->groupdata as $k => &$v) {
//                $ruledata[$v['id']] = $v['name'];
//            }
//            unset($v);
//            $this ->assign('groupdata', $ruledata);
            $result = [];
            $groups = $this->auth->getGroups();

            foreach ($groups as $m => $n) {
                $childlist = Tree::instance()->getTreeList(Tree::instance()->getTreeArray($n['id']));
                $temp = [];
                foreach ($childlist as $k => $v) {
                    $temp[$v['id']] = $v['name'];
                }
                $result[__($n['name'])] = $temp;
            }
            $groupdata = $result;
        }
        $this->assign('groupdata', $groupdata);
    }
    public function index(){
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
//            $childrenGroupIds = $this->childrenGroupIds;
            $groupName = AuthGroup::where('id', 'in', $this->childrenGroupIds)
                ->column('name', 'id');
            $authGroupList = AuthGroupAccess::where('group_id', 'in', $this->childrenGroupIds)
                ->field('uid,group_id')
                ->select();
            $adminGroupName = [];
            foreach ($authGroupList as $k => $v) {
                if (isset($groupName[$v['group_id']])) {
                    $adminGroupName[$v['uid']][$v['group_id']] = $groupName[$v['group_id']];
                }
            }
            $groups = $this->auth->getGroups();
            foreach ($groups as $m => $n) {
                $adminGroupName[$this->auth->id][$n['id']] = $n['name'];
            }
            [$where, $sort, $order, $offset, $limit] = $this->buildparams();
            $total = $this->model
                ->where($where)
                ->where('id', 'in', $this->childrenAdminIds)
                ->order($sort, $order)
                ->count();
            $list = $this->model
                ->where($where)
                ->where('id', 'in', $this->childrenAdminIds)
                ->hidden(['password', 'salt', 'token'])
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            foreach ($list as $k => &$v) {
                $groups = isset($adminGroupName[$v['id']]) ? $adminGroupName[$v['id']] : [];
                $v['groups'] = implode(',', array_keys($groups));
                $v['groups_text'] = implode(',', array_values($groups));
            }
            unset($v);
            $result = ['total' => $total, 'rows' => $list];
            return json($result);
        }
        return $this-> fetch();

    }

    public function add(){
        if ($this->request->isPost()) {
            $params = $this->request->post('row/a');
            if($params){
                $data['username'] = $params['username'];
                $data['salt'] = Random::alnum();
                $data['password'] = md5(md5($params['password']).$data['salt']);
                $data['email'] = $params['email'];
                $data['nickname'] = $params['nickname'];
                $data['status'] = $params['status'];
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
    public function edit($ids = null){
        $row = $this->model->find($ids);
        if (! $row) {
            $this->error(__('No Results were found'));
        }
        if ($this->request->isPost()) {
            $params = $this->request->post('row/a');
            if ($params) {
                if ($params['password']) {
                    $params['salt'] = Random::alnum();
                    $params['password'] = md5(md5($params['password']).$params['salt']);
                } else {
                    unset($params['password'], $params['salt']);
                }
                //这里需要针对username和email做唯一验证
                $adminValidate = validate('Admin.edit', [], false, false);
                $adminValidate->rule([
                    'username' => 'require|max:50|unique:admin,username,'.$row->id,
                    'email'    => 'require|email|unique:admin,email,'.$row->id,
                ]);
                $rs = $adminValidate->check($params);
                if (! $rs) {
                    $this->error($adminValidate->getError());
                }
                $result = $row->save($params);
                if ($result === false) {
                    $this->error($row->getError());
                }
                // 先移除所有权限
                AuthGroupAccess::where('uid', $row->id)->delete();
                $group = $this->request->post('group/a');
                // 过滤不允许的组别,避免越权
                $group = array_intersect($this->childrenGroupIds, $group);
                $dataset = [];
                foreach ($group as $value) {
                    $dataset[] = ['uid' => $row->id, 'group_id' => $value];
                }
                //AuthGroupAccess::saveAll($dataset);
                $model = new AuthGroupAccess();
                $model->saveAll($dataset);
                $this->success();
            }
            $this->error();
        }
        $grouplist = $this->auth->getGroups($row['id']);
        $groupids = [];
        foreach ($grouplist as $k => $v) {
            $groupids[] = $v['id'];
        }
        $this->assign('row', $row);
        $this->assign('groupids', $groupids);

        return $this->fetch();
    }

    /**
     * 删除.
     */
    public function del($ids = '')
    {
        if ($ids) {
            // 避免越权删除管理员
            $childrenGroupIds = $this->childrenGroupIds;
            $adminList = $this->model->where('id', 'in', $ids)->where('id', 'in',
                function ($query) use ($childrenGroupIds) {
                    $query->name('auth_group_access')->where('group_id', 'in', $childrenGroupIds)->field('uid');
                })->select();
            if ($adminList) {
                $deleteIds = [];
                foreach ($adminList as $k => $v) {
                    $deleteIds[] = $v->id;
                }
                $deleteIds = array_diff($deleteIds, [$this->auth->id]);
                if ($deleteIds) {
                    $this->model->destroy($deleteIds);
                    AuthGroupAccess::where('uid', 'in', $deleteIds)->delete();
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

    /**
     * 下拉搜索.
     */
    public function selectpage()
    {
        $this->dataLimit = 'auth';
        $this->dataLimitField = 'id';

        return parent::selectpage();
    }
}
