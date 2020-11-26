<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/8/17
 * Time: 14:53
 */

error_reporting(0);
use think\facade\Db;
use think\facade\Cache;
//use app\api\model\BusinessApplicationPlatform;

//use think\response\Json;
header('Content-Type: text/html;charset=utf-8');
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:*');
header("Access-Control-Allow-Headers:DNT,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type, Accept-Language, Origin, Accept-Encoding");
header('Access-Control-Allow-Credentials:true');//表示是否允许发送Cookie

define('SOF_NAME','[吾景]:');
define('API_HOST',$_SERVER['HTTP_HOST']);//获取当前域名

define('SHUJUCUNCHU',app()->getRootPath().'public');
if(!is_dir(SHUJUCUNCHU)){
    mkdir(SHUJUCUNCHU,0777,true);
}




//判断系统平台
function smsyem_version(){
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') || strpos($_SERVER['HTTP_USER_AGENT'], 'iPad')) {
        $app_version = "IOS";
    } else if (strpos($_SERVER['HTTP_USER_AGENT'], 'Android')) {
        $app_version = "Android";
    } else {
        $app_version = "other";
    }
    return $app_version;
}



//文件下载
function downloadFile($path){
    $file_path = SHUJUCUNCHU .$path;
    if (file_exists($file_path)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($file_path));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));
        ob_clean();
        flush();
        readfile($file_path);
        exit;
    }
}


/**
 * @param $data  要加密的字符串
 * @param $key   密钥
 * @return string
 */
if (!function_exists('encrypt')) {
    function encrypt($data, $key = 'encrypt')
    {
        $key = md5($key);
        $x = 0;
        $len = strlen($data);
        $l = strlen($key);
        $char = '';
        for ($i = 0; $i < $len; $i++) {
            if ($x == $l) {
                $x = 0;
            }
            $char .= $key{$x};
            $x++;
        }
        $str = '';
        for ($i = 0; $i < $len; $i++){
            $str .= chr(ord($data{$i}) + (ord($char{$i})) % 256);
        }
        $str = $key . $str;
        return base64_encode($str);
    }
}

if (!function_exists('getplatformMsg')) {
    function getplatformMsg ($plat)
    {
        /*if (!Cache::get($plat . '_setting_message')) {
            $data = BusinessApplicationPlatform::where('name', $plat)->find();
            Cache::set($plat . '_setting_message', $data, 86400);
        } else {
            $data = Cache::get($plat . '_setting_message');
        }*/
        $data = BusinessApplicationPlatform::where('name', $plat)->find();
        return $data;
    }
}
/**
 * post/https请求
 * @param $url
 * @param $post
 * @return bool|string
 */
if (!function_exists('post_curls')) {
    function post_curls($url, $post)
    {
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post); // Post提交的数据包
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $res = curl_exec($curl); // 执行操作
        if (curl_errno($curl)) {
            echo 'Errno'.curl_error($curl);//捕抓异常
        }
        curl_close($curl); // 关闭CURL会话
        return $res; // 返回数据，json格式
    }
}

//日期函数
function date_ymd(){
    return date('Y-m-d H:i:s',time());
}

function sp_log($content,$file="log.txt"){
    file_put_contents($file, $content,FILE_APPEND);
}

//  获取13位时间戳
function getMillisecond(){
    list($t1, $t2) = explode(' ', microtime());
    return sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
}
/** 把网络图片图片转成base64
 * @param string $img 图片地址
 * @return string
 * $type = 1 需要压缩
 */
/*网络图片转为base64编码*/
function imgtobase64($img='',$type=0,$iwidth="",$iheight=""){
    if($type == 1){
        $img = resize_image($img,$iwidth,$iheight);
    }
    $imageInfo = getimagesize($img);
//    $base64 = "" . chunk_split(base64_encode(file_get_contents($img)));
    $img = 'data:' . $imageInfo['mime'] . ';base64,' . chunk_split(base64_encode(file_get_contents($img)));
    return $img;
}
/**
 * 按照指定的尺寸压缩图片
 * @param $source_path  原图路径
 * @param $dest  保存路径
 * @param $imgWidth     目标宽度
 * @param $imgHeight    目标高度
 * @return bool|string
 */
function resize_image($source_path,$imgWidth,$imgHeight){
    $dest = './storage/urlimage/';
    $source_info = getimagesize($source_path);
    $source_mime = $source_info['mime'];
    switch ($source_mime) {
        case 'image/gif':
            $source_image = imagecreatefromgif($source_path);
            break;

        case 'image/jpeg':
            $source_image = imagecreatefromjpeg($source_path);
            break;

        case 'image/png':
            $source_image = imagecreatefrompng($source_path);
            break;

        default:
            return false;
            break;
    }
    $target_image    = imagecreatetruecolor($imgWidth, $imgHeight); //创建一个彩色的底图
    imagecopyresampled($target_image, $source_image, 0, 0, 0, 0, $imgWidth, $imgHeight, $source_info[0], $source_info[1]);
    $fileName = $dest.date("YmdHis").uniqid().'.jpg';
    if(!imagejpeg($target_image,'./'.$fileName)){
        $fileName = '';
    }
    imagedestroy($target_image);
    return $fileName;
}



//本地图片转换成base64编码
function base64EncodeImage ($image_file) {
    $base64_image = '';
    $image_info = getimagesize($image_file);
    $image_data = fread(fopen($image_file, 'r'), filesize($image_file));
    $base64_image = 'data:' . $image_info['mime'] . ';base64,' . chunk_split(base64_encode($image_data));
    return $base64_image;
}




/**
 * post请求  请求ACCESS_TOKEN
 * Enter description here ...
 * @param unknown_type $url
 * @param unknown_type $curlPost
 */
function curl_post_json($url, $data){
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60); //设置超时
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
    $rtn = curl_exec($ch);
    curl_close($ch);
    return $rtn;
}


/**
 * curl post请求函数
 * @url    请求的地址
 * @data   传输的数据
 * @json   是否json传输
 * @arr_header   请求头信息
 */
function curl_post1($url, $curlPost){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_REFERER, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

function http_request_post($url,$data = null,$json = false,$arr_header = null){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    if (!empty($data)){
        if ($json && is_array($data)) {
            $data = json_encode($data);
        }
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    if(!empty($arr_header)){
        curl_setopt($curl, CURLOPT_HTTPHEADER, $arr_header);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    // echo curl_getinfo($curl);
    curl_close($curl);
    unset($curl);
    return $output;
}

function getSign($publicData){
    //判断是否为存在
    if(!is_array($publicData) || empty($publicData)){
        return false;
    }
    //付给一个变量
    $nimei=$publicData['client_secret'];
    //清除数组中的参数
    unset($publicData['client_secret']);
    //按照字母进行排序
    ksort($publicData);
    //将client_secret拼接到字符串的前面
    $sign=$nimei;
    //把排序后的结果按照参数名+参数值的方式拼接
    foreach ($publicData as $k=>$v){
        $sign.=$k.$v;
    }
    //将client_secret拼接到字符串的后面
    $sign.=$nimei;
    //md5加密后的结果转为大写
    $sign=strtoupper(md5($sign));
    $publicData['sign']=$sign;
    return $publicData;
}

/**
 * 随机字符串生成
 * @param int $len 生成的字符串长度
 * @return string
 */
function sp_random_string($len = 6) {
    $chars = array(
        "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
        "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
        "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
        "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
        "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
        "3", "4", "5", "6", "7", "8", "9"
    );
    $charsLen = count($chars) - 1;
    shuffle($chars);    // 将数组打乱
    $output = "";
    for ($i = 0; $i < $len; $i++) {
        $output .= $chars[mt_rand(0, $charsLen)];
    }
    return $output;
}
/**
 * 随机字符串生成 英文
 * @param int $len 生成的字符串长度
 * @return string
 */
function sp_random_string_en($len = 6) {
    $chars = array(
        "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
        "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
        "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
        "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
        "S", "T", "U", "V", "W", "X", "Y", "Z"
    );
    $charsLen = count($chars) - 1;
    shuffle($chars);    // 将数组打乱
    $output = "";
    for ($i = 0; $i < $len; $i++) {
        $output .= $chars[mt_rand(0, $charsLen)];
    }
    return $output;
}

//获取日期
function getday(){
    $date = [
        'threeday' => date("Y-m-d",strtotime("+3 day")),//3天后
        'sevenday' => date("Y-m-d",strtotime("+7 day")),//7天后
        'week' => date("Y-m-d",strtotime("+1 week")),//一周天后
        'month' => date("Y-m-d",strtotime("+1 month")),//一月后
        'month3' => date("Y-m-d",strtotime("+3 month")),//一季度后
        'month6' => date("Y-m-d",strtotime("+6 month")),//半年后
        'year' => date("Y-m-d",strtotime("+1 year")),//一年后
    ];
    return $date;
}


function strarray($str){
    $arr = explode(',',$str);
    return $arr;
}

//日志函数  $logtype 表示 1 正常操作日志 2 代表充值记录
function user_log($username,$uid,$content,$logtype=1){
    $data = [
        'username'=>$username,
        'logip' => get_client_ip(),
        'logtime'=> time(),
        'uid'=> $uid,
        'logtype'=> $logtype,
        'content' => $content
    ];
    Db::name('user_log') -> insert($data);
}
