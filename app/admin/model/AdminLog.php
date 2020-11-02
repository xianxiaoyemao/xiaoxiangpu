<?php
/**
 * Created by PhpStorm.
 * User: LENOVO
 * Date: 2020/10/5
 * Time: 21:29
 */
namespace app\admin\model;
use app\common\model\BaseModel;
use app\admin\library\Auth;
class AdminLog extends BaseModel{
    public static function adminlog($title,$content){
        $auth = Auth::instance();
        $admin_id = $auth->isLogin() ? $auth -> id : 0;
        $username = $auth->isLogin() ? $auth-> username : __('Unknown');
        if (! $content) {
            $content = request()->param();
            foreach ($content as $k => $v) {
                if (is_string($v) && strlen($v) > 200 || stripos($k, 'password') !== false) {
                    unset($content[$k]);
                }
            }
        }
        if (!$title) {
            $title = [];
            $breadcrumb = Auth::instance()->getBreadcrumb();
            foreach ($breadcrumb as $k => $v) {
                $title[] = $v['title'];
            }
            $title = implode(' ', $title);
        }
        self::create([
            'title'     => $title,
            'content'   => ! is_scalar($content) ? json_encode($content) : $content,
            'url'       => substr(request()->url(), 0, 1500),
            'admin_id'  => $admin_id,
            'username'  => $username,
            'useragent' => substr(request()->server('HTTP_USER_AGENT'), 0, 255),
            'ip'        => request()->ip(),
            'createtime' => time()
        ]);
    }

    public function admin(){
        return $this->belongsTo('Admin', 'admin_id');
    }
}