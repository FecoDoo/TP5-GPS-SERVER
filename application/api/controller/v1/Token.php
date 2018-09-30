<?php
namespace app\api\controller\v1;

use app\api\controller\Send;
use app\api\controller\Oauth;
use think\facade\Cache;
use think\Request;
use think\Db;
/**
 * 生成token
 */
class Token
{
	use Send;

	/**
	 * 请求时间差
	 */
	public static $timeDif = 10000;

	public static $accessTokenPrefix = 'accessToken_';
	public static $refreshAccessTokenPrefix = 'refreshAccessToken_';
	// public static $expires = 7200;
	public static $expires = 60*60*24*30;
	public static $refreshExpires = 60*60*24*30;   //刷新token过期时间
	/**
	 * 测试appid，正式请数据库进行相关验证
	 */
	public static $appid = 'gps';
	/**
	 * appsercet
	 */
	public static $appsercet = '123456';

	/**
	 * 生成token
	 */
	public function token(Request $request)
	{
		//参数验证
		$validate = new \app\api\validate\Token;
		$header = $request->header('');
		$data = [
			'appid' => $header['appid'],
			'mobile' => $header['mobile'],
			'passwd' => $header['passwd'],
			'nonce' => $header['nonce'],
			'timestamp' => $header['timestamp'],
			'sign' => $header['sign']
		];
		
		if(!$validate->check($data)){
			return self::returnMsg(401,$validate->getError());
		}

		//参数校验
		self::checkParams($data);

		//用户校验
		$res = Db::table('student')->where('mobile',$data['mobile'])->find();
		if (empty($res)){
			return self::returnMsg(500,'user does not exit');
		} else if ($data['passwd'] !== $res['password']) {
			return self::returnMsg(400, 'password not correct');
		}
		
		$data['uid'] = $res['id'];

		try {
			$accessToken = self::setAccessToken($data);  //传入参数应该是根据手机号查询改用户的数据
			return self::returnMsg(200,'success',$accessToken);
		} catch (Exception $e) {
			return self::returnMsg(500,'fail',$e);
		}
	}

	/**
	 * 刷新token
	 */
	public function refresh($refresh_token='',$appid = '')
	{
		//查看刷新token是否存在
		// $cache_refresh_token = Cache::get(self::$refreshAccessTokenPrefix.$appid);
		$db_refresh_token = '';
		$refresh_token_exist = 0;
		
		try {
			if (!empty($db_refresh_token = Db::table('token')->where('refresh_token', $refresh_token)->find())){
				$refresh_token_exist = 1; // Not null 
			} else {
				$refresh_token_exist = 0;
			}
		} catch (Exception $e) {
			return self::returnMsg(500,'fail',$e);
		}

		if($refresh_token_exist){
			return self::returnMsg(401,'fail','refresh_token is null');
		}else{
			if($db_refresh_token !== $refresh_token){
				return self::returnMsg(401,'fail','refresh_token is error');
			}else{    //重新给用户生成调用token
				$data['appid'] = $appid;
				$accessToken = self::setAccessToken($data); 
				return self::returnMsg(200,'success',$accessToken);
			}
		}
	}

	/**
	 * 参数检测
	 */
	public static function checkParams($params = [])
	{	
		//时间戳校验
		if(abs($params['timestamp'] - time()) > self::$timeDif){
			return self::returnMsg(401,'请求时间戳与服务器时间戳异常','timestamp：'.time());
		}

		//appid检测，这里是在本地进行测试，正式的应该是查找数据库或者redis进行验证
		if($params['appid'] !== self::$appid){
			return self::returnMsg(401,'appid 错误');
		}

		//签名检测
		$sign = Oauth::makeSign($params,self::$appsercet);
		if($sign !== $params['sign']){
			return self::returnMsg(401,'sign错误','sign：'.$sign);
		}
	}

	/**
	 * 设置AccessToken
	 * @param $clientInfo
	 * @return int
	 */
	protected function setAccessToken($clientInfo)
	{
		//参数初始化
		$access_token = '';
		$refresh_token = '';
		$accessTokenInfo = [];

		//判断是否已存在当前用户的Token
		$res = Db::table('token')->where('mobile',$clientInfo['mobile'])->find();
		if (!empty($res)) {
			//获取令牌
			$accessTokenInfo = [
				'access_token' => $res['access_token'],
				'expires_time' => $res['expires_time'],
				'refresh_token' => $res['refresh_token'],
				'refresh_expires_time' => $res['refresh_expires_time'],
			] + $clientInfo;
		} else {
			//生成令牌
			$access_token = self::buildAccessToken();
			$refresh_token = self::getRefreshToken();

			$accessTokenInfo = [
				'access_token'  => $access_token,//访问令牌
				'expires_time'  => time() + self::$expires,//过期时间时间戳
				'refresh_token' => $refresh_token,//刷新的token
				'refresh_expires_time'  => time() + self::$refreshExpires,//过期时间时间戳
			] + $clientInfo;

			//保存本次token
			self::saveAccessToken($accessTokenInfo);
		}

		unset($accessTokenInfo['nonce'],$accessTokenInfo['passwd']);
		return $accessTokenInfo;
	}

	/**
	 * 刷新用的token检测是否还有效
	 */
	public static function getRefreshToken($appid = '')
	{
		return self::buildAccessToken();
		// return Cache::get(self::$refreshAccessTokenPrefix.$appid) ? Cache::get(self::$refreshAccessTokenPrefix.$appid) : self::buildAccessToken(); 
	}

	/**
	 * 生成AccessToken
	 * @return string
	 */
	protected static function buildAccessToken($lenght = 32)
	{
		//生成AccessToken
		$str_pol = "1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789abcdefghijklmnopqrstuvwxyz";
		return substr(str_shuffle($str_pol), 0, $lenght);

	}

	/**
	 * 存储token
	 * @param $accessToken
	 * @param $accessTokenInfo
	 */
	protected static function saveAccessToken($accessTokenInfo)
	{
		//存储accessToken
		try {
			Db::table('token')->strict(false)->insert($accessTokenInfo);
			// cache(self::$accessTokenPrefix . $accessToken, $accessTokenInfo, self::$expires);
		} catch (Exception $e) {
			return self::returnMsg(500, 'fail', $e);
		}
	}

	/**
	 * 刷新token存储
	 * @param $accessToken
	 * @param $accessTokenInfo
	 */
	protected static function saveRefreshToken($refresh_token,$appid)
	{
		//存储RefreshToken
		// cache(self::$refreshAccessTokenPrefix.$appid,$refresh_token,self::$refreshExpires);
	}
}