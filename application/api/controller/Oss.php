<?php
namespace app\api\controller;

use think\Controller;
use think\Request;

trait Oss
{
	private static $id='LTAIergYt01cBfX1';
	private static $key='FZQl7b3v7iuYF9KSOcrvTqThUOVmOz';

	public function PortraitOss($id)
	{
		// $host的格式为 bucketname.endpoint，请替换为您的真实信息
		$host = 'http://gps-server.oss-cn-shenzhen.aliyuncs.com';  
		// $callbackUrl为上传回调服务器的URL，请将下面的IP和Port配置为您自己的真实URL信息
		$callbackUrl = 'http://47.106.64.85/v1/student/getPortraitCallback';
		// 用户上传文件时指定的前缀
		$dir = 'portrait_';
		
		$callback_param = [
			'callbackUrl'=>$callbackUrl, 
			'callbackBody'=>'filename=${object}&size=${size}&mimeType=${mimeType}&height=${imageInfo.height}&width=${imageInfo.width}',
			'callbackBodyType'=>"application/x-www-form-urlencoded"
		];
		$base64_callback_body = base64_encode(json_encode($callback_param));
		//设置该policy超时时间
		$expire = 600;
		$expiration = date("Y-m-d H:i:s" ,time()+$expire);

		$conditions = [
			['content-length-range'=>'0-1048576000'],
			['starts-with' => $dir]
		];

		$arr = [
			'expiration'=> $expiration,
			'conditions'=> $conditions
		];
		
		$policy = json_encode($arr);

		$base64_policy = base64_encode($policy);
		$signature = base64_encode(hash('sha1', $base64_policy, self::$key));

		$response = [
			'accessid' => self::$id,
			'host' => $host,
			'policy' => $base64_policy,
			'signature' => $signature,
			'expire' => $expiration,
			'callback' => $base64_callback_body,
			'dir' => 'image/'
		];
		return $response;
	}

	public function PortraitCallback()
	{
		// 1.获取OSS的签名header和公钥url header
		$authorizationBase64 = '';
		$pubKeyUrlBase64 = '';
		/*
		 * 注意：如果要使用HTTP_AUTHORIZATION头，你需要先在apache或者nginx中设置rewrite，以apache为例，修改
		 * 配置文件/etc/httpd/conf/httpd.conf(以你的apache安装路径为准)，在DirectoryIndex index.php这行下面增加以下两行
			RewriteEngine On
			RewriteRule .* - [env=HTTP_AUTHORIZATION:%{HTTP:Authorization},last]
		 * */
		if (isset($_SERVER['HTTP_AUTHORIZATION']))
		{
			$authorizationBase64 = $_SERVER['HTTP_AUTHORIZATION'];
		}
		if (isset($_SERVER['HTTP_X_OSS_PUB_KEY_URL']))
		{
			$pubKeyUrlBase64 = $_SERVER['HTTP_X_OSS_PUB_KEY_URL'];
		}

		if ($authorizationBase64 == '' || $pubKeyUrlBase64 == '')
		{
			header("http/1.1 403 Forbidden");
			exit();
		}

		// 2.获取OSS的签名
		$authorization = base64_decode($authorizationBase64);

		// 3.获取公钥
		$pubKeyUrl = base64_decode($pubKeyUrlBase64);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $pubKeyUrl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		$pubKey = curl_exec($ch);
		if ($pubKey == "")
		{
			//header("http/1.1 403 Forbidden");
			exit();
		}

		// 4.获取回调body
		$body = file_get_contents('php://input');

		// 5.拼接待签名字符串
		$authStr = '';
		$path = $_SERVER['REQUEST_URI'];
		$pos = strpos($path, '?');
		if ($pos === false)
		{
			$authStr = urldecode($path)."\n".$body;
		}
		else
		{
			$authStr = urldecode(substr($path, 0, $pos)).substr($path, $pos, strlen($path) - $pos)."\n".$body;
		}

		// 6.验证签名
		$ok = openssl_verify($authStr, $authorization, $pubKey, OPENSSL_ALGO_MD5);
		if ($ok == 1)
		{
			header("Content-Type: application/json");
			$data = array("Status"=>"Ok");
			echo json_encode($data);
		}
		else
		{
			//header("http/1.1 403 Forbidden");
			exit();
		}
	}
}

