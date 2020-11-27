<?php
// Author: www.arpher.cn Date: 2016/6/19 Time: 1:54
/**
 * *多图上传**
 * $upload=new Exclfile("uploads/goods/");
 * //原图地址
 * $up=$upload->saveNative();
 * //原图地址
 * $urlpath=$up['urlpath'];
 * //图片名称
 * $basename=$up['basename'];
 * $biguploadDir = 'uploads/goods/'.'/big/';
 * $miduploadDir = 'uploads/goods/'.'/mid/';
 * $smalluploadDir = 'uploads/goods/'.'/small/';
 * $BigPath = $this->thumb($urlpath,$biguploadDir.$basename,800,800,1);
 * $MiddlePath = $this->thumb($urlpath,$miduploadDir.$basename,480,480,1);
 * $SmallPath = $this->thumb($urlpath,$smalluploadDir.$basename,60,60,1);
 * exit(json_encode(array('pic'=>$urlpath)));
 * *多图上传end**
 *
 */
namespace fast;
class Exclfile{
    private $saveDir;//图片保存的路径//文件保存目录
    private $fileName;//上传图片的前缀名称
    private $fileType=['rar','zip'];//压缩格式
    private $imgType=['jpg','jpeg','gif','png'];//图片格式
    //private $uploaddir="./file/";//文件保存目录
    private $uploaddir_db="/file/";
    private $musicType=['mp3','WAV','ogg'];//允许上传文件的类型//音频格式
    private $patch="./";//程序所在路径
	private $videoType=['mp4','avi','ogg'];//视频频格式
    /**
     * [__construct description]
     * @param [type] $saveDir  保存路径
     * @param [type] $fileName 随机名称
     * @param [type] $fileType 图片格式
     */
    public function __construct($saveDir,$fileName=''){
        //赋值
        $this -> saveDir = $saveDir;
        $this -> fileName = $fileName;
    }

   //生成文件名
   private function filenamecreate($length){
        $hash='CR-';
        $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
        $max=strlen($chars)-1;
        mt_srand((double)microtime()*1000000);
        for($i=0;$i<$length;$i++){
            $hash=$chars[mt_rand(0,$max)];
        }
        return $hash;
    }
    //上传软件
    public function ImgSaceUpload($software,$uplaodsrc){
        if(empty($_FILES)){
            return 0;
        }else{
            // 临时文件
            $tempFile  = $_FILES["$software"]['tmp_name'];
            //判断上传文件是否超限
            if($_FILES["$software"]['size']>1024*1024*2) {
                die('{"status":0,"msg":"上传文件不能超过2M"}');
            }
            // 文件信息
            $fileParts = pathinfo($_FILES["$software"]['name']);
            if (!in_array($fileParts['extension'],$this -> imgType)) {
                die('{"status":0,"msg":"上传文件为jpg,jpeg,gif,png格式"}');
            }
            $upsrc= $uplaodsrc.$software.'/'.date('Ymd');
            if(!is_dir($this-> saveDir.$upsrc.'/')) {
                mkdir($this-> saveDir.$upsrc.'/',0777,true);
            }
            // 保存文件
            $filename= time().'-'.rand(1000, 9999999)."." . $fileParts['extension'];
            $url = $upsrc.'/'.$filename;
            $pathurl = $this-> saveDir.$url;
            //save
            move_uploaded_file($tempFile,$pathurl);
            //back
            return $url;
        }
    }
    //上传软件
    public function software($software,$uplaodsrc){
    	$ext = ['apk','ios','wgt','yml','exe'];
        if(empty($_FILES)){
            return 0;
        }else{
            // 临时文件
            $tempFile  = $_FILES["$software"]['tmp_name'];
            //判断上传文件是否超限
            if($_FILES["$software"]['size']>1024*1024*50) {
                die('{"status":0,"msg":"上传文件不能超过25M"}');
            }
            // 文件信息
            $fileParts = pathinfo($_FILES["$software"]['name']);
            if (!in_array($fileParts['extension'],$ext)) {
                die('{"status":0,"msg":"上传文件为apk、ios、wgt、yml、exe格式"}');
            }
            $patharr = explode('_',$fileParts['filename']);
            // 判断文件夹是否存在
            if(!is_dir($this-> saveDir.$uplaodsrc.$patharr[0].'/')) {
                mkdir($this-> saveDir.$uplaodsrc.$patharr[0].'/',0777,true);
            }
            // 保存文件
            $filename= $patharr[0] . "." . $fileParts['extension'];
            $url = $uplaodsrc.$patharr[0].'/'.$filename;
            //存放地址
            $pathurl=$this-> saveDir.$url;
            //save
            move_uploaded_file($tempFile,$pathurl);
            //back
            return $url;
        }
    }
    /**
     * 图片保存
     * @return str 图片地址
     */
    public function saveImg(){
        // 判断有无文件
        if(empty($_FILES)){
            return 0;
        }else{
            // 临时文件
            $tempFile  = $_FILES['imageFile']['tmp_name'];
            //判断上传文件是否超限
            if($_FILES['imageFile']['size']>3145728){
                die('{"status":0,"msg":"上传文件不能超过3MB"}');
            }
            // 文件信息
            $fileParts = pathinfo($_FILES['imageFile']['name']);

            if (!in_array($fileParts['extension'],$this->imgType)) {
                die('{"status":0,"msg":"上传文件为jpg、jpeg、gif、png格式"}');
            }
            // 判断文件夹是否存在
            if(!is_dir($this-> saveDir))
            {
                mkdir($this-> saveDir,0777,true);
            }
            // 保存文件
            $filename= $this-> fileName . time().'-' .rand(1000, 99999999) . "." . $fileParts['extension'];
            //存放地址
            $url=$this->saveDir.$filename;
            //save
            move_uploaded_file($tempFile,$url);
            //back
            return $url;
        }
    }
    public function savemusic(){
        if(empty($_FILES)){
            return 0;
        }else{
            // 临时文件
            $tempFile  = $_FILES['musicFile']['tmp_name'];
                        //判断上传文件是否超限
            if($_FILES['musicFile']['size']>1024*1024*5) {
                die('{"status":0,"msg":"上传文件不能超过5M"}');
            }
            // 文件信息
           $fileParts = pathinfo($_FILES['musicFile']['name']);
           if (!in_array($fileParts['extension'],$this -> musicType)) {
                die('{"status":0,"msg":"上传文件为mp3、ogg格式"}');
            }else{
               $filename=explode(".",$tempFile);
               do{
                   $filename[0]= $this -> filenamecreate(10);
                   $name_f=implode(".",$filename);
                   $url=$this -> saveDir.$name_f;//存放地址
                   //$uploadfile_db=$this->$uploaddir_db.$name_f;
               }while(file_exists($url));
               if(move_uploaded_file($tempFile,$url)){//上传
                   if(is_uploaded_file($tempFile)){
                       echo"上传失败！";
                   }
                   else{
                       echo"上传成功";
                   }
               }
               return $url;
           }
        }
    }
    /**
     * 压缩文件保存
     * @return str 压缩文件地址
     */
    public function saveFile(){
        // 判断有无文件
        if(empty($_FILES)){
            return 0;
        }else{
            // 临时文件
            $tempFile  = $_FILES['zipFile']['tmp_name'];
            //判断上传文件是否超限
            if($_FILES['zipFile']['size']>1024*1024*20)
            {
                die('{"status":0,"msg":"上传文件不能超过20M"}');
            }
            // 文件信息
            $fileParts = pathinfo($_FILES['zipFile']['name']);

            if (!in_array($fileParts['extension'],$this->fileType))
            {
                die('{"status":0,"msg":"上传文件为zip、rar格式"}');
            }
            // 判断文件夹是否存在
            if(!is_dir($this-> saveDir))
            {
                mkdir($this-> saveDir,0777,true);
            }
            // 保存文件
            $filename= $this-> fileName . time() .'-'. rand(1000, 9999999999) . "." . $fileParts['extension'];
            //存放地址
            $Url=$this->saveDir.$filename;
            //save
            if(move_uploaded_file($tempFile,$Url))
            {
                echo json_encode(array('error'=>0,'fileurl'=>'/'.$Url));
            }else{
                echo json_encode(array('error'=>-1,'msg'=>'上传错误!'));
            }
            exit;
        }
    }



    //具体操作
    public function saveImg1(){
        if(empty($_FILES)) return false;
        //临时文件
        $tmpFile=$_FILES['imgFile']['tmp_name'];
        //获取文件信息
        $fileParts=pathinfo($_FILES['imgFile']['name']);
        //判断是否是图片格式
        if(!in_array($fileParts['extension'],$this->imgType))
        {
            exit(json_encode(array('status'=>0,'msg'=>'图片格式不正确!')));
        }
        //判断图片的大小
        if($_FILES['imgFile']['size']>3145728)
        {
            exit(json_encode(array('status'=>0,'msg'=>'图片上传不能超过3MB!')));
        }
        //判断有没有上传文件夹
        if(!is_dir($this->saveDir))
        {
            mkdir($this->saveDir,0777,true);
        }
        //拼接路径
        $filename=$this->fileName."-".time().rand(1000,9999).".".$fileParts['extension'];
        $destination=$this->saveDir.$filename;
        move_uploaded_file($tmpFile, $destination);
        //返回全路径地址
        return $destination;
    }

    //多图上传
    public function saveNative(){
        // 判断有无文件
        if(empty($_FILES)){
            return 0;
        }else{
            // 临时文件
            $tempFile  = $_FILES['file']['tmp_name'];
            //判断上传文件是否超限
            if($_FILES['file']['size']>5242880)
            {
                die('{"status":0,"msg":"上传文件不能超过5MB"}');
            }
            // 文件信息
            $fileParts = pathinfo($_FILES['file']['name']);

            if (!in_array($fileParts['extension'],$this->imgType))
            {
                die('{"status":0,"msg":"上传文件为jpg、jpeg、gif、png格式"}');
            }
            // 判断文件夹是否存在
            if(!is_dir($this-> saveDir))
            {
                mkdir($this-> saveDir,0777,true);
            }
            // 保存文件
            $filename= $this-> fileName . time() .'_'.rand(1000, 9999999999) . "." . $fileParts['extension'];
            //存放地址
            $url=$this->saveDir.$filename;
            //save
            move_uploaded_file($tempFile,$url);
            //back
            return [
                'urlpath' => $url,//原图地址
                'basename' => $filename
            ];
        }
    }
//    //上传视频
//    public function savevideo(){
//        if(empty($_FILES)){
//            return 0;
//        }else{
//            // 临时文件
//            $tempFile  = $_FILES['videoFile']['tmp_name'];
//            //判断上传文件是否超限
//            if($_FILES['videoFile']['size']>10485760){
//                die('{"status":0,"msg":"上传文件不能超过10MB"}');
//            }
//            // 文件信息
//            $fileParts = pathinfo($_FILES['videoFile']['name']);
//
//            if (!in_array($fileParts['extension'],$this->videoFile))
//            {
//                die('{"status":0,"msg":"上传文件为mp4、avi、fvi"}');
//            }
//            // 判断文件夹是否存在
//            if(!is_dir($this-> saveDir))
//            {
//                mkdir($this-> saveDir,0777,true);
//            }
//            // 保存文件
//            $filename= $this-> fileName . time().rand(1000, 99999) . "." . $fileParts['extension'];
//            //存放地址
//            $url=$this->saveDir.$filename;
//            //save
//            move_uploaded_file($tempFile,$url);
//            //back
//            return $url;
//        }
//    }
    /**
     * 图片裁切处理
     * @param $img 原图
     * @param string $outFile 另存文件名
     * @param string $thumbWidth 缩略图宽度
     * @param string $thumbHeight 缩略图高度
     * @param string $thumbType 裁切图片的方式
     * 1 固定宽度  高度自增 2固定高度  宽度自增 3固定宽度  高度裁切
     * 4 固定高度 宽度裁切 5缩放最大边 原图不裁切 6缩略图尺寸不变，自动裁切最大边
     * @return bool|string
     */
    public function thumb($img, $outFile, $thumbWidth, $thumbHeight, $thumbType){
        //基础配置
        $thumbType = $thumbType;
        $thumbWidth = $thumbWidth;
        $thumbHeight = $thumbHeight;
        //获得图像信息
        $imgInfo = getimagesize($img);
        $imgWidth = $imgInfo [0];
        $imgHeight = $imgInfo [1];
        $imgType = image_type_to_extension($imgInfo [2]);
        //获得相关尺寸
        $thumb_size = $this->thumbSize($imgWidth, $imgHeight, $thumbWidth, $thumbHeight, $thumbType);
        //原始图像资源
        $func = "imagecreatefrom" . substr($imgType, 1);
        $resImg = $func($img);
        //缩略图的资源
        if ($imgType == '.gif') {
            $res_thumb = imagecreate($thumb_size [0], $thumb_size [1]);
            $color = imagecolorallocate($res_thumb, 255, 0, 0);
        } else {
            $res_thumb = imagecreatetruecolor($thumb_size [0], $thumb_size [1]);
            imagealphablending($res_thumb, false); //关闭混色
            imagesavealpha($res_thumb, true); //储存透明通道
        }
        //绘制缩略图X
        if (function_exists("imagecopyresampled")) {
            imagecopyresampled($res_thumb, $resImg, 0, 0, 0, 0, $thumb_size [0], $thumb_size [1], $thumb_size [2], $thumb_size [3]);
        } else {
            imagecopyresized($res_thumb, $resImg, 0, 0, 0, 0, $thumb_size [0], $thumb_size [1], $thumb_size [2], $thumb_size [3]);
        }
        //处理透明色
        if ($imgType == '.gif') {
            imagecolortransparent($res_thumb, $color);
        }
        //配置输出文件名
        $imgInfo = pathinfo($img);

        is_dir(dirname($outFile)) || mkdir(dirname($outFile),0755,true);
        $func = "image" . substr($imgType, 1);
        $func($res_thumb, $outFile);
        if (isset($resImg))
            imagedestroy($resImg);
        if (isset($res_thumb))
            imagedestroy($res_thumb);
        return $outFile;
    }

    /**
     * 获得缩略图的尺寸信息
     * @param $imgWidth 原图宽度
     * @param $imgHeight 原图高度
     * @param $thumbWidth 缩略图宽度
     * @param $thumbHeight 缩略图的高度
     * @param $thumbType 处理方式
     * 1 固定宽度  高度自增 2固定高度  宽度自增 3固定宽度  高度裁切
     * 4 固定高度 宽度裁切 5缩放最大边 原图不裁切
     * @return mixed
     */
    private function thumbSize($imgWidth, $imgHeight, $thumbWidth, $thumbHeight, $thumbType){
        //初始化缩略图尺寸
        $w = $thumbWidth;
        $h = $thumbHeight;
        //初始化原图尺寸
        $cuthumbWidth = $imgWidth;
        $cuthumbHeight = $imgHeight;
        switch ($thumbType) {
            case 1 :
                //固定宽度  高度自增
            $h = $thumbWidth / $imgWidth * $imgHeight;
            break;
            case 2 :
                //固定高度  宽度自增
            $w = $thumbHeight / $imgHeight * $imgWidth;
            break;
            case 3 :
                //固定宽度  高度裁切
            $cuthumbHeight = $imgWidth / $thumbWidth * $thumbHeight;
            break;
            case 4 :
                //固定高度  宽度裁切
            $cuthumbWidth = $imgHeight / $thumbHeight * $thumbWidth;
            break;
            case 5 :
                //缩放最大边 原图不裁切
            if (($imgWidth / $thumbWidth) > ($imgHeight / $thumbHeight)) {
                $h = $thumbWidth / $imgWidth * $imgHeight;
            } elseif (($imgWidth / $thumbWidth) < ($imgHeight / $thumbHeight)) {
                $w = $thumbHeight / $imgHeight * $imgWidth;
            } else {
                $w = $thumbWidth;
                $h = $thumbHeight;
            }
            break;
            default:
                //缩略图尺寸不变，自动裁切图片
            if (($imgHeight / $thumbHeight) < ($imgWidth / $thumbWidth))
            {
                $cuthumbWidth = $imgHeight / $thumbHeight * $thumbWidth;
            }
            elseif (($imgHeight / $thumbHeight) > ($imgWidth / $thumbWidth))
            {
                $cuthumbHeight = $imgWidth / $thumbWidth * $thumbHeight;
            }
        }
        $arr [0] = $w;
        $arr [1] = $h;
        $arr [2] = $cuthumbWidth;
        $arr [3] = $cuthumbHeight;
        return $arr;
    }

//	public function xheditor(){
//		$inputName='filedata';//表单文件域name
//		$attachDir='./public/UploadFiles/Uploads';//上传文件保存路径，结尾不要带/
//		$dirType=1;//1:按天存入目录 2:按月存入目录 3:按扩展名存目录  建议使用按天存
//		$maxAttachSize=1097152;//最大上传大小，默认是2M
//		$upExt='txt,rar,zip,jpg,jpeg,gif,png,swf,wmv,avi,wma,mp3,mid,mp4';//上传扩展名
//		$msgType=2;//返回上传参数的格式：1，只返回url，2，返回参数数组
//		$immediate=isset($_GET['immediate'])?$_GET['immediate']:0;//立即上传模式，仅为演示用
//
//		$err = "";
//		$msg = "''";
//		$tempPath=$attachDir.'/'.date("YmdHis").mt_rand(10000,99999).'.tmp';
//		$localName='';
//
//		if(isset($_SERVER['HTTP_CONTENT_DISPOSITION'])&&preg_match('/attachment;\s+name="(.+?)";\s+filename="(.+?)"/i',$_SERVER['HTTP_CONTENT_DISPOSITION'],$info)){//HTML5上传
//			file_put_contents($tempPath,file_get_contents("php://input"));
//			$localName=urldecode($info[2]);
//		}
//		else{//标准表单式上传
//			$upfile=@$_FILES[$inputName];
//			if(!isset($upfile))$err='文件域的name错误';
//			elseif(!empty($upfile['error'])){
//				switch($upfile['error'])
//				{
//					case '1':
//						$err = '文件大小超过了php.ini定义的upload_max_filesize值';
//						break;
//					case '2':
//						$err = '文件大小超过了HTML定义的MAX_FILE_SIZE值';
//						break;
//					case '3':
//						$err = '文件上传不完全';
//						break;
//					case '4':
//						$err = '无文件上传';
//						break;
//					case '6':
//						$err = '缺少临时文件夹';
//						break;
//					case '7':
//						$err = '写文件失败';
//						break;
//					case '8':
//						$err = '上传被其它扩展中断';
//						break;
//					case '999':
//					default:
//						$err = '无有效错误代码';
//				}
//			}
//			elseif(empty($upfile['tmp_name']) || $upfile['tmp_name'] == 'none')$err = '无文件上传';
//			else{
//				move_uploaded_file($upfile['tmp_name'],$tempPath);
//				$localName=$upfile['name'];
//			}
//		}
//
//		if($err==''){
//			$fileInfo=pathinfo($localName);
//			$extension=$fileInfo['extension'];
//			if(preg_match('/^('.str_replace(',','|',$upExt).')$/i',$extension))
//			{
//				$bytes=filesize($tempPath);
//				if($bytes > $maxAttachSize)$err='请不要上传大小超过'.$this->formatBytes($maxAttachSize).'的文件';
//				else
//				{
//					switch($dirType)
//					{
//						case 1: $attachSubDir = 'day_'.date('ymd'); break;
//						case 2: $attachSubDir = 'month_'.date('ym'); break;
//						case 3: $attachSubDir = 'ext_'.$extension; break;
//					}
//					$attachDir = $attachDir.'/'.$attachSubDir;
//					if(!is_dir($attachDir))
//					{
//						@mkdir($attachDir, 0777);
//						@fclose(fopen($attachDir.'/index.htm', 'w'));
//					}
//					PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
//					$newFilename=date("YmdHis").mt_rand(1000,9999).'.'.$extension;
//					$targetPath = $attachDir.'/'.$newFilename;
//
//					rename($tempPath,$targetPath);
//					@chmod($targetPath,0755);
//					$targetPath=$this->jsonString(str_replace('./Data','/Data',__ROOT__.$targetPath));
//					if($immediate=='1')$targetPath='!'.$targetPath;
//					if($msgType==1)$msg="'$targetPath'";
//					else $msg="{'url':'".$targetPath."','localname':'".$this->jsonString($localName)."','id':'1'}";//id参数固定不变，仅供演示，实际项目中可以是数据库ID
//				}
//			}
//			else $err='上传文件扩展名必需为：'.$upExt;
//
//			@unlink($tempPath);
//		}
//
//		echo "{'err':'".$this->jsonString($err)."','msg':".$msg."}";
//	}
//	public function jsonString($str)
//	{
//		return preg_replace("/([\\\\\/'])/",'\\\$1',$str);
//	}
//	public function formatBytes($bytes) {
//		if($bytes >= 1073741824) {
//			$bytes = round($bytes / 1073741824 * 100) / 100 . 'GB';
//		} elseif($bytes >= 1048576) {
//			$bytes = round($bytes / 1048576 * 100) / 100 . 'MB';
//		} elseif($bytes >= 1024) {
//			$bytes = round($bytes / 1024 * 100) / 100 . 'KB';
//		} else {
//			$bytes = $bytes . 'Bytes';
//		}
//		return $bytes;
//	}
}
