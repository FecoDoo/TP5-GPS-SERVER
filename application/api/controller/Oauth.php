<?php
namespace app\api\controller;

use app\api\controller\Send;
use think\Exception;
use think\facade\Request;
use think\facade\Cache;
use think\Db;
/**
 * API鉴权验证
 */
class Oauth
{
	use Send;
	
	/**
	 * accessToken存储前缀
	 *
	 * @var string
	 */
	public static $accessTokenPrefix = 'accessToken_';

	/**
	 * 过期时间秒数
	 *
	 * @var int
	 */
	public static $expires = 86400;

	/**
	 * 认证授权 通过用户信息和路由
	 * @param Request $request
	 * @return \Exception|UnauthorizedException|mixed|Exception
	 * @throws UnauthorizedException
	 */
	final function authenticate()
	{      
		return self::certification(self::getClient());
	}

	/**
	 * 获取用户信息
	 * @param Request $request
	 * @return $this
	 * @throws UnauthorizedException
	 */
	public static function getClient()
	{   
		//获取头部信息
		try {
			// $authorization = Request::header('authentication');   //tp5.1Facade调用 获取头部字段
			// $authorization = explode(" ", $authorization);  //authorization：USERID xxxx
			// $authorizationInfo  = explode(":", base64_decode($authorization[1]));
			// $clientInfo['uid'] = $authorizationInfo[2];
			// $clientInfo['appid'] = $authorizationInfo[0];
			// $clientInfo['access_token'] = $authorizationInfo[1];
			$authorization = Request::header('');
			$clientInfo['uid'] = $authorization['uid'];
			$clientInfo['appid'] = $authorization['appid'];
			$clientInfo['access_token'] = $authorization['access_token'];
			return $clientInfo;
		} catch (Exception $e) {
			return self::returnMsg(401,'Invalid authorization credentials',Request::header(''));
		}
	}

	/**
	 * 获取用户信息后 验证权限
	 * @return mixed
	 */
	public static function certification($data = []){

		// $getCacheAccessToken = Cache::get(self::$accessTokenPrefix . $data['access_token']);  //获取缓存access_token
		$getAccessToken = Db::table('token')->where('uid', $data['uid'])->find(); 

		if(empty($getAccessToken)){
			return self::returnMsg(401,'fail','数据库中无当前登录信息，请重新登陆');
		}
		if($getAccessToken['appid'] !== $data['appid']){
			return self::returnMsg(401,'fail',"appid错误");  //appid与数据库中的appid不匹配
		}
		if($getAccessToken['access_token'] !== $data['access_token']){
			return self::returnMsg(401,'fail','access_token不存在或为空');  //uid与数据库中的uid不匹配
		}
		return $data;
	}

	/**
	 * 生成签名
	 * _字符开头的变量不参与签名
	 */
	public static function makeSign ($data = [],$app_secret = '')
	{   
		unset($data['version']);
		unset($data['sign']);
		return self::_getOrderMd5($data,$app_secret);
	}

	/**
	 * 计算ORDER的MD5签名
	 */
	private static function _getOrderMd5($params = [] , $app_secret = '') {
		ksort($params);
		$params['key'] = $app_secret;
		return strtolower(md5(urldecode(http_build_query($params))));
	}

}