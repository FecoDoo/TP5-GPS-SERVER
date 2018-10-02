<?php

namespace app\api\controller\v1;

use think\Controller;
use think\Request;
//use app\api\controller\Api;
use app\api\controller\Send;
use think\Db;

class Reg
{
	use Send;

	public function register(Request $request)
	{
		$data = input('');
		self::checkData($data);

		if (!$data['type']) {
			self::student($data);
		} else {
			self::enterprise($data);
		}
	}

	//学生注册
	public function student($data = [])
	{
		$res = Db::table('student')->where('mobile',$data['mobile'])->find();
		if (empty($res)) {
			try {
				unset($data['type']);
				$res = Db::table('student')->strict(false)->insert($data);
				unset($data['password']);
				self::returnMsg(200,'OK',$data);
			} catch (Exception $e) {
				self::returnMsg(401,'插入失败',$e);
			}
		} else {
			self::returnMsg(200,'账号已存在');
		}
	}

	//企业注册
	public function enterprise($data = [])
	{
		// $res = Db::table('enterprise')->where('mobile',$data['mobile'])->find();
		// if (empty($res)) {
		// 	try {
		// 		unset($data['type']);
		// 		$res = Db::table('enterprise')->insert($data);
		// 		unset($data['password']);
		// 		self::returnMsg(200,'OK',$data);
		// 	} catch (Exception $e) {
		// 		self::returnMsg(401,'插入失败',$e);
		// 	}
		// } else {
		// 	self::returnMsg(200,'账号已存在');
		// }
	}
	/////////////////////////////工具函数
	


	//参数验证
	private static function checkData($data = [],$scene = 'student')
	{	
		$validate = new \app\api\validate\Reg;
		if(!$validate->check($data,'',$scene)){
			return self::returnMsg(401,$validate->getError());
		}
	}
}