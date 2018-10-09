<?php
namespace app\api\controller\v1

use think\Controller;
use think\Request;
use app\api\controller\Api;
use app\api\controller\Send;
use think\Db;

class Enterprise extends Api
{
	use Send;

	private static $id= 'LTAIergYt01cBfX1';
	private static $key= 'FZQl7b3v7iuYF9KSOcrvTqThUOVmOz';

    // $host的格式为 bucketname.endpointx， 请替换为您的真实信息。
    $host = 'http://gps-server.oss-cn-shenzhen.aliyuncs.com';  

    // $callbackUrl为上传回调服务器的URL， 请将下面的IP和Port配置为您自己的真实URL信息。
    $callbackUrl = 'http://88.88.88.88:8888/aliyun-oss-appserver-php/php/callback.php';

    $dir = 'user-dir-prefix/';          // 上传文件时指定的前缀。
}