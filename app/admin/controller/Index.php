<?php
/**
 * Created by PhpStorm.
 * User: LENOVO
 * Date: 2020/7/16
 * Time: 20:39
 */
namespace app\admin\controller;
use think\Validate;
use think\facade\Event;
use think\facade\Config;
use think\Facade\DB;
use app\admin\model\AdminLog;
use app\common\controller\Backend;
class Index extends Backend{
    protected $noNeedLogin = ['login'];
    protected $noNeedRight = ['index', 'logout'];
    protected $layout = '';

    public function initialize(){
        parent::initialize();
    }
    /**
     * 后台首页.
     */
    public function index(){
        //左侧菜单
        [$menulist, $navlist, $fixedmenu, $referermenu] = $this->auth->getSidebar([
            'dashboard' => 'hot',
            'addon'     => ['new', 'red', 'badge'],
            'auth/rule' => __('Menu'),
            'general'   => ['new', 'purple'],
        ]);
        $action = $this->request->request('action');
        if ($this->request->isPost()) {
            if ($action == 'refreshmenu') {
                $this->success('', null, ['menulist' => $menulist, 'navlist' => $navlist]);
            }
        }
        $this->assign('menulist', $menulist);
        $this->assign('navlist', $navlist);
        $this->assign('fixedmenu', $fixedmenu);
        $this->assign('referermenu', $referermenu);
        $this->assign('title', __('Home'));
        return $this -> fetch();
    }

    /**
     * 管理员登录.
     */
    public function login(){
        $url = $this->request->get('url', 'index/index');
        if ($this->auth->isLogin()) {
            $this->success(__("You've logged in, do not login again"), $url);
        }
        if ($this->request->isPost()) {
            $username = $this->request->post('username');
            $password = $this->request->post('password');
            $keeplogin = $this->request->post('keeplogin');
            $rule = [
                'username'  => 'require|length:3,30',
                'password'  => 'require|length:3,30',
            ];
            $data = [
                'username'  => $username,
                'password'  => $password,
            ];
            if (Config::get('fastadmin.login_captcha')) {
                $rule['captcha'] = 'require|captcha';
                $data['captcha'] = $this->request->post('captcha');
            }
            $validate = new Validate($rule, [],
                ['username' => __('Username'), 'password' => __('Password'), 'captcha' => __('Captcha')]);
            $result = $validate->check($data);
            if (! $result) {
                $this->error($validate->getError(), $url, ['token' => $this->request->buildToken()]);
            }

            $result = $this->auth->login($username, $password, $keeplogin ? 86400 : 0);
            if ($result === true) {
                AdminLog::adminlog('登录',$this->request->post());
                Event::trigger('admin_login_after', $this->request);
                $this->success(__('Login successful'), $url,
                    ['url' => $url, 'id' => $this->auth->id, 'username' => $username, 'avatar' => $this->auth->avatar]);
            } else {
                $msg = $this->auth->getError();
                $msg = $msg ? $msg : __('Username or password is incorrect');
                $this->error($msg, $url, ['token' => $this->request->buildToken()]);
            }
        }
        // 根据客户端的cookie,判断是否可以自动登录
        if ($this->auth->autologin()) {
            $this->redirect($url);
        }
//        $background = Config::get('site')['login_background'];
//        epre($background['login_background']) ;exit;
//        $background = stripos($background, 'http') === 0 ? $background : config('site.cdnurl').$background;
//        $this->assign('background', $background);
        $this->assign('title', __('Login'));
        Event::trigger('admin_login_init', $this->request);
        return $this -> fetch();
    }
    /**
     * 注销登录.
     */
    public function logout(){
        $this->auth->logout();
        Event::trigger('admin_logout_after', $this->request);
        $this->success(__('Logout successful'), 'index/login');
    }
}