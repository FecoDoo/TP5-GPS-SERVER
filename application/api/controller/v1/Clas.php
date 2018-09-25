<?php

namespace app\api\controller\v1;

use think\Controller;
use think\Request;
use app\api\controller\Api;
use app\api\controller\Send;
use think\Db;

class Clas extends Api
{
	use Send;

	public function index()
	{
		self::returnMsg(200,'OK',$this->clientInfo);
	}

	//查看班级信息
	public function info(Request $request)
	{
		$data = input('');
		$this->checkData($data);
		
		$res = Db::table('class')->where('id',$data['cid'])->find();

		if (empty($res)) {
			self::returnMsg(401,'未找到该班级信息');
		} else {
			$course = Db::table('course')->where('cid',$data['cid'])->select();
			unset($res['id'],$course['cid'],$course['id']);
			$res = [
				'class' => $res,
				'course' => $course
			];
			self::returnMsg(200,'OK',$res);
		}
	}

	/////////////////////////////工具函数

	//参数验证
	private static function checkData($data = [],$type = 'info')
	{	
		$validate = new \app\api\validate\Clas;
		if(!$validate->check($data,'',$type)){
			return self::returnMsg(401,$validate->getError());
		}
	}
}