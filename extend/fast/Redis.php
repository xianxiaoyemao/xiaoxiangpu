<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/8/27
 * Time: 18:20
 */
namespace fast;
use think\facade\Config;
use think\facade\Cache;
class Redis{
    protected static $con = null;
    public function __construct(){
        $con = new \Redis();
        $con->connect(config('queue.connections.redis.host'), config('queue.connections.redis.port'), 5);
        self::$con = $con;
    }
    public static function handler(){
        if(self::$con){
            return self::$con;
        }else{
            $self = new self();
            return self::$con;
        }
    }
    /*********************队列操作命令************************/
    /**
     * 在队列尾部插入一个元素
     * @param unknown $key
     * @param unknown $value
     * 返回队列长度
     */
    public static function rPush($key,$value){
        return self::handler()->rPush($key,$value);
    }

    /**
     * 在队列尾部插入一个元素 如果key不存在，什么也不做
     * @param unknown $key
     * @param unknown $value
     * 返回队列长度
     */
    public static function rPushx($key,$value){
        return self::handler()->rPushx($key,$value);
    }

    /**
     * 在队列头部插入一个元素
     * @param unknown $key
     * @param unknown $value
     * 返回队列长度
     */
    public static function lPush($key,$value){
        return self::handler()->lPush($key,$value);
    }

    /**
     * 在队列头插入一个元素 如果key不存在，什么也不做
     * @param unknown $key
     * @param unknown $value
     * 返回队列长度
     */
    public static function lPushx($key,$value){
        return self::handler()->lPushx($key,$value);
    }

    /**
     * 返回队列长度
     * @param unknown $key
     */
    public static function lLen($key)
    {
        return self::handler()->lLen($key);
    }

    /**
     * 返回队列指定区间的元素
     * @param unknown $key
     * @param unknown $start
     * @param unknown $end
     */
    public static function lRange($key,$start,$end)
    {
        return self::handler()->lrange($key,$start,$end);
    }

    /**
     * 返回队列中指定索引的元素
     * @param unknown $key
     * @param unknown $index
     */
    public static function lIndex($key,$index)
    {
        return self::handler()->lIndex($key,$index);
    }

    /**
     * 设定队列中指定index的值。
     * @param unknown $key
     * @param unknown $index
     * @param unknown $value
     */
    public static function lSet($key,$index,$value)
    {
        return self::handler()->lSet($key,$index,$value);
    }

    /**
     * 删除值为vaule的count个元素
     * PHP-REDIS扩展的数据顺序与命令的顺序不太一样，不知道是不是bug
     * count>0 从尾部开始
     *  >0　从头部开始
     *  =0　删除全部
     * @param unknown $key
     * @param unknown $count
     * @param unknown $value
     */
    public static function lRem($key,$count,$value)
    {
        return self::handler()->lRem($key,$value,$count);
    }

    /**
     * 删除并返回队列中的头元素。
     * @param unknown $key
     */
    public static function lPop($key)
    {
        return self::handler()->lPop($key);
    }

    /**
     * 删除并返回队列中的尾元素
     * @param unknown $key
     */
    public static function rPop($key){
        return self::handler()->rPop($key);
    }

    /*************redis字符串操作命令*****************/

    /**
     * 设置一个key
     * @param unknown $key
     * @param unknown $value
     */
    public static function set($key,$value){
        return self::handler()->set($key,$value);
//        return self::handler()->set($key,json_encode($value));
    }
//    public static function setarr($key,$arr){
//        self::handler()->set($key,json_encode($arr));
//    }


    /**
     * 得到一个key
     * @param unknown $key
     */
    public static function get($key){
        return self::handler()->get($key);
//        $value = self::handler()->get($key);
//        if($value){
//            return json_decode($value,true);
//        }else{
//            return [];
//        }
    }
    /**
     * 设置一个有过期时间的key
     * @param unknown $key
     * @param unknown $expire
     * @param unknown $value
     */
    public static function setex($key,$expire,$value){
        return self::handler()->setex($key,$expire,$value);
//        return self::handler()->setex($key,$expire,json_encode($value));
    }

    /**
     * 设置一个key,如果key存在,不做任何操作.
     * @param unknown $key
     * @param unknown $value
     */
    public static function setnx($key,$value)
    {
        return self::handler()->setnx($key,$value);
    }

    /**
     * 批量设置key
     * @param unknown $arr
     */
    public static function mset($arr)
    {
        return self::handler()->mset($arr);
    }

    public static function incr($key){
        return self::handler()->incr($key);
    }

    public static function exists($key){
        return self::handler()->exists($key);
    }

    /*****************hash表操作函数*******************/

    /**
     * 得到hash表中一个字段的值
     * @param string $key 缓存key
     * @param string  $field 字段
     * @return string|false
     */
    public static function hGet($key,$field){
        return  self::handler()->hGet($key,$field);
    }

    /**
     * 为hash表设定一个字段的值
     * @param string $key 缓存key
     * @param string  $field 字段
     * @param string $value 值。
     * @return bool
     */
    public static function hSet($key,$field,$value){
        return self::handler() ->hSet($key,$field,$value);
    }

    /**
     * 判断hash表中，指定field是不是存在
     * @param string $key 缓存key
     * @param string  $field 字段
     * @return bool
     */
    public static function hExists($key,$field){
        return self::handler()->hExists($key,$field);
    }

    /**
     * 删除hash表中指定字段 ,支持批量删除
     * @param string $key 缓存key
     * @param string  $field 字段
     * @return int
     */
    public static function hdel($key,$field){
        $fieldArr=explode(',',$field);
        $delNum=0;

        foreach($fieldArr as $row)
        {
            $row=trim($row);
            $delNum+=self::handler()->hDel($key,$row);
        }

        return $delNum;
    }

    /**
     * 返回hash表元素个数
     * @param string $key 缓存key
     * @return int|bool
     */
    public static function hLen($key){
        return self::handler() ->hLen($key);
    }

    /**
     * 为hash表设定一个字段的值,如果字段存在，返回false
     * @param string $key 缓存key
     * @param string  $field 字段
     * @param string $value 值。
     * @return bool
     */
    public static function hSetNx($key,$field,$value){
        return self::handler()->hSetNx($key,$field,$value);
    }

    /**
     * 为hash表多个字段设定值。
     * @param string $key
     * @param array $value
     * @return array|bool
     */
    public static function hMset($key,$value){
        if(!is_array($value))
            return false;
        return self::handler()->hMset($key,$value);
    }

    /**
     * 为hash表多个字段设定值。
     * @param string $key
     * @param array|string $value string以','号分隔字段
     * @return array|bool
     */
    public static function hMget($key,$field){
        if(!is_array($field))
            $field=explode(',', $field);
        return self::handler()->hMget($key,$field);
    }

    /**
     * 为hash表设这累加，可以负数
     * @param string $key
     * @param int $field
     * @param string $value
     * @return bool
     */
    public static function hIncrBy($key,$field,$value){
        $value=intval($value);
        return self::handler()->hIncrBy($key,$field,$value);
    }

    /**
     * 返回所有hash表的所有字段
     * @param string $key
     * @return array|bool
     */
    public static function hKeys($key){
        return self::handler()->hKeys($key);
    }

    /**
     * 返回所有hash表的字段值，为一个索引数组
     * @param string $key
     * @return array|bool
     */
    public static function hVals($key){
        return self::handler()->hVals($key);
    }

    /**
     * 返回所有hash表的字段值，为一个关联数组
     * @param string $key
     * @return array|bool
     */
    public static function hGetAll($key){
        return self::handler()->hGetAll($key);
    }

    /*********************有序集合操作*********************/

    /**
     * 给当前集合添加一个元素
     * 如果value已经存在，会更新order的值。
     * @param string $key
     * @param string $order 序号
     * @param string $value 值
     * @return bool
     */
    public static function zAdd($key,$order,$value){
        return self::handler()->zAdd($key,$order,$value);
    }

    /**
     * 给$value成员的order值，增加$num,可以为负数
     * @param string $key
     * @param string $num 序号
     * @param string $value 值
     * @return 返回新的order
     */
    public static function zinCry($key,$num,$value){
        return self::handler()->zinCry($key,$num,$value);
    }

    /**
     * 删除值为value的元素
     * @param string $key
     * @param stirng $value
     * @return bool
     */
    public static function zRem($key,$value){
        return self::handler()->zRem($key,$value);
    }

    /**
     * 集合以order递增排列后，0表示第一个元素，-1表示最后一个元素
     * @param string $key
     * @param int $start
     * @param int $end
     * @return array|bool
     */
    public static function zRange($key,$start,$end){
        return self::handler()->zRange($key,$start,$end);
    }

    /**
     * 集合以order递减排列后，0表示第一个元素，-1表示最后一个元素
     * @param string $key
     * @param int $start
     * @param int $end
     * @return array|bool
     */
    public static function zRevRange($key,$start,$end){
        return self::handler()->zRevRange($key,$start,$end);
    }

    /**
     * 集合以order递增排列后，返回指定order之间的元素。
     * min和max可以是-inf和+inf　表示最大值，最小值
     * @param string $key
     * @param int $start
     * @param int $end
     * @package array $option 参数
     *     withscores=>true，表示数组下标为Order值，默认返回索引数组
     *     limit=>array(0,1) 表示从0开始，取一条记录。
     * @return array|bool
     */
    public static function zRangeByScore($key,$start='-inf',$end="+inf",$option=array()){
        return self::handler()->zRangeByScore($key,$start,$end,$option);
    }

    /**
     * 集合以order递减排列后，返回指定order之间的元素。
     * min和max可以是-inf和+inf　表示最大值，最小值
     * @param string $key
     * @param int $start
     * @param int $end
     * @package array $option 参数
     *     withscores=>true，表示数组下标为Order值，默认返回索引数组
     *     limit=>array(0,1) 表示从0开始，取一条记录。
     * @return array|bool
     */
    public static function zRevRangeByScore($key,$start='-inf',$end="+inf",$option=array()){
        return self::handler()->zRevRangeByScore($key,$start,$end,$option);
    }

    /**
     * 返回order值在start end之间的数量
     * @param unknown $key
     * @param unknown $start
     * @param unknown $end
     */
    public static function zCount($key,$start,$end){
        return self::handler()->zCount($key,$start,$end);
    }

    /**
     * 返回值为value的order值
     * @param unknown $key
     * @param unknown $value
     */
    /**
     * @param null|\Redis $con
     */
    public static function setCon(?\Redis $con): void{
        self::$con = $con;
    } function zScore($key,$value){
        return self::handler()->zScore($key,$value);
    }

    /**
     * 返回集合以score递增加排序后，指定成员的排序号，从0开始。
     * @param unknown $key
     * @param unknown $value
     */
    public static function zRank($key,$value){
        return self::handler()->zRank($key,$value);
    }

    /**
     * 返回集合以score递增加排序后，指定成员的排序号，从0开始。
     * @param unknown $key
     * @param unknown $value
     */
    public function zRevRank($key,$value){
        return self::handler()->zRevRank($key,$value);
    }

    /**
     * 删除集合中，score值在start end之间的元素　包括start end
     * min和max可以是-inf和+inf　表示最大值，最小值
     * @param unknown $key
     * @param unknown $start
     * @param unknown $end
     * @return 删除成员的数量。
     */
    public static function zRemRangeByScore($key,$start,$end){
        return self::handler()->zRemRangeByScore($key,$start,$end);
    }

    /**
     * 返回集合元素个数。
     * @param unknown $key
     */
    public static function zCard($key){
        return self::handler()->zCard($key);
    }




    /*************redis　无序集合操作命令*****************/

    /**
     * 返回集合中所有元素
     * @param unknown $key
     */
    public static function sMembers($key)
    {
        return self::handler()->sMembers($key);
    }

    /**
     * 求2个集合的差集
     * @param unknown $key1
     * @param unknown $key2
     */
    public static function sDiff($key1,$key2)
    {
        return self::handler()->sDiff($key1,$key2);
    }

    /**
     * 添加集合。由于版本问题，扩展不支持批量添加。这里做了封装
     * @param unknown $key
     * @param string|array $value
     */
    public static function sAdd($key,$value)
    {
        if(!is_array($value))
            $arr=array($value);
        else
            $arr=$value;
        foreach($arr as $row)
            self::handler()->sAdd($key,$row);
    }

    /**
     * 返回无序集合的元素个数
     * @param unknown $key
     */
    public static function scard($key)
    {
        return self::handler()->scard($key);
    }

    /**
     * 从集合中删除一个元素
     * @param unknown $key
     * @param unknown $value
     */
    public static function srem($key,$value)
    {
        return self::handler()->srem($key,$value);
    }

    /*************redis管理操作命令*****************/

    /**
     * 选择数据库
     * @param int $dbId 数据库ID号
     * @return bool
     */
    public  function select($dbId){
        $this->dbId=$dbId;
        return self::handler()->select($dbId);
    }

    /**
     * 清空当前数据库
     * @return bool
     */
    public static function flushDB()
    {
        return self::handler()->flushDB();
    }

    /**
     * 返回当前库状态
     * @return array
     */
    public function info()
    {
        return self::handler()->info();
    }

    /**
     * 同步保存数据到磁盘
     */
    public function save()
    {
        return self::handler()->save();
    }

    /**
     * 异步保存数据到磁盘
     */
    public function bgSave()
    {
        return self::handler()->bgSave();
    }

    /**
     * 返回最后保存到磁盘的时间
     */
    public function lastSave()
    {
        return self::handler()->lastSave();
    }

    /**
     * 返回key,支持*多个字符，?一个字符
     * 只有*　表示全部
     * @param string $key
     * @return array
     */
    public function keys($key)
    {
        return self::handler()->keys($key);
    }

    /**
     * 删除指定key
     * @param unknown $key
     */
    public static function del($key)
    {
        return self::handler()->del($key);
    }

    /**
     * 为一个key设定过期时间 单位为秒
     * @param unknown $key
     * @param unknown $expire
     */
    public static function expire($key,$expire)
    {
        return self::handler()->expire($key,$expire);
    }

    /**
     * 返回一个key还有多久过期，单位秒
     * @param unknown $key
     */
    public static function ttl($key)
    {
        return self::handler()->ttl($key);
    }

    /**
     * 设定一个key什么时候过期，time为一个时间戳
     * @param unknown $key
     * @param unknown $time
     */
    public static function exprieAt($key,$time)
    {
        return self::handler()->expireAt($key,$time);
    }

    /**
     * 关闭服务器链接
     */
    public static function close()
    {
        return self::handler()->close();
    }

    /**
     * 关闭所有连接
     */
    public  static function closeAll()
    {
        foreach(static::$_instance as $o)
        {
            if($o instanceof self)
                $o->close();
        }
    }

    /** 这里不关闭连接，因为session写入会在所有对象销毁之后。
    public function __destruct()
    {
    return self::handler()->close();
    }
     **/
    /**
     * 返回当前数据库key数量
     */
    public function dbSize()
    {
        return self::handler()->dbSize();
    }

    /**
     * 返回一个随机key
     */
    public static function randomKey()
    {
        return self::handler()->randomKey();
    }


    /*********************事务的相关方法************************/

    /**
     * 监控key,就是一个或多个key添加一个乐观锁
     * 在此期间如果key的值如果发生的改变，刚不能为key设定值
     * 可以重新取得Key的值。
     * @param unknown $key
     */
    public static function watch($key)
    {
        return self::handler()->watch($key);
    }

    /**
     * 取消当前链接对所有key的watch
     *  EXEC 命令或 DISCARD 命令先被执行了的话，那么就不需要再执行 UNWATCH 了
     */
    public static function unwatch()
    {
        return self::handler()->unwatch();
    }

    /**
     * 开启一个事务
     * 事务的调用有两种模式Redis::MULTI和Redis::PIPELINE，
     * 默认是Redis::MULTI模式，
     * Redis::PIPELINE管道模式速度更快，但没有任何保证原子性有可能造成数据的丢失
     */
    public static function multi($type=\Redis::MULTI)
    {
        return self::handler()->multi($type);
    }

    /**
     * 执行一个事务
     * 收到 EXEC 命令后进入事务执行，事务中任意命令执行失败，其余的命令依然被执行
     */
    public static function exec()
    {
        return self::handler()->exec();
    }

    /**
     * 回滚一个事务
     */
    public static function discard()
    {
        return self::handler()->discard();
    }

    /**
     * 测试当前链接是不是已经失效
     * 没有失效返回+PONG
     * 失效返回false
     */
    public static function ping()
    {
        return self::handler()->ping();
    }

    public function auth($auth)
    {
        return self::handler()->auth($auth);
    }
    /*********************自定义的方法,用于简化操作************************/

    /**
     * 得到一组的ID号
     * @param unknown $prefix
     * @param unknown $ids
     */
    public function hashAll($prefix,$ids)
    {
        if($ids==false)
            return false;
        if(is_string($ids))
            $ids=explode(',', $ids);
        $arr=array();
        foreach($ids as $id)
        {
            $key=$prefix.'.'.$id;
            $res=$this->hGetAll($key);
            if($res!=false)
                $arr[]=$res;
        }

        return $arr;
    }

    /**
     * 生成一条消息，放在redis数据库中。使用0号库。
     * @param string|array $msg
     */
    public function pushMessage($lkey,$msg)
    {
        if(is_array($msg)){
            $msg    =    json_encode($msg);
        }
        $key    =    md5($msg);

        //如果消息已经存在，删除旧消息，已当前消息为准
        //echo $n=$this->lRem($lkey, 0, $key)."\n";
        //重新设置新消息
        $this->lPush($lkey, $key);
        $this->setex($key, 3600, $msg);
        return $key;
    }


    /**
     * 得到条批量删除key的命令
     * @param unknown $keys
     * @param unknown $dbId
     */
    public function delKeys($keys,$dbId)
    {
        $redisInfo=$this->getConnInfo();
        $cmdArr=array(
            'redis-cli',
            '-a',
            $redisInfo['auth'],
            '-h',
            $redisInfo['host'],
            '-p',
            $redisInfo['port'],
            '-n',
            $dbId,
        );
        $redisStr=implode(' ', $cmdArr);
        $cmd="{$redisStr} KEYS \"{$keys}\" | xargs {$redisStr} del";
        return $cmd;
    }
}
