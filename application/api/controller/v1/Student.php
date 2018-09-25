<?php

namespace app\api\controller\v1;

use think\Controller;
use think\Request;
use app\api\controller\Api;
use app\api\controller\Send;
use think\Db;

class Student extends Api
{
	use Send;

	/**
	 * 显示资源列表
	 *
	 * @return \think\Response
	 */
	public function index()
	{
		self::returnMsg(200,'OK',$this->clientInfo);
	}

	/**
	 * 显示学生信息
	 *
	 * @param  int  $id
	 * @return \think\Response
	 */
	public function info()
	{
		$res = Db::table('student')->where('id',$this->clientInfo['uid'])->find();
		if (empty($res)) {
			self::returnMsg(401,'查询失败',$res);
		} else {
			unset($res['password'],$res['id']);
			self::returnMsg(200,'OK',$res);
		}
	}

	// public function classScore()
	// {
	// 	$id = Db::table('token')->where('access_token',$this->clientInfo['access_token'])->value('uid');
	// 	try{
	// 		$res = Db::table('stu_class')->where('sid',$id)->select();
	// 		if (empty($res)) {
	// 			self::returnMsg(401,'你未参加任何课程');
	// 		} else {
	// 			$info = Db::table('student')->where('id',$id)->find();
				
	// 			$count1 = Db::table('stu_class')->where('sid',$id)->count();
	// 			$count2 = 0;
	// 			for ($i = 0;$i<$count1;$i++){
	// 				$count2 += Db::table('course')->where('id',$res['cid'])->count();
	// 			}
	// 			$temp = array();
	// 			$count = Db::table('class')->where('id',$res['cid'])->count();
				
	// 			for ($i = 0;$i<$count;$i++){
	// 				$class = Db::table('class')->where('id',$value['cid'])->value('name');
	// 				$temp[$i] = $class;
	// 			}	
	// 		}
	// 		$data = [
	// 			'name' => $info['name'],
	// 			'school' => $info['school'],
	// 			'score' => $temp
	// 		];
	// 		self::returnMsg(200,'OK',$data);
	// 	} catch (Exception $e) {
	// 		self::returnMsg(401,'Failed',$e);
	// 	}
	// }

	/**
	 * 更改班级信息 
	 */
	public function changeClass(Request $request)
	{
		
		$data = input('');
		$this->checkData($data,'class');
		
		//type 0退选 1加入
		if ($data['type']) {
			$res = Db::table('stu_class')->where('sid',$this->clientInfo['uid'])->where('cid',$data['cid'])->find();
			if (empty($res)) {
				
				$res = Db::table('class')->where('id',$data['cid'])->find();
				if (empty($res)) {
					self::returnMsg(401,'查无此班级');
				} else {
					$insert = [
						'sid' => $this->clientInfo['uid'],
						'cid' => $data['cid'],
					];
					try {	
						Db::table('stu_class')->insert($insert);
						self::returnMsg(200,'OK');
					} catch (Exception $e) {
						self::returnMsg(401,'选择班级失败',$e);
					}
				}
				
			} else {
				self::returnMsg(401,'已在该班级内');
			}
		} else {
			$res = Db::table('stu_class')->where('sid',$this->clientInfo['uid'])->where('cid',$data['cid'])->find();

			if (empty($res)) {
				self::returnMsg(401,'你不在该班级内');
			} else {
				$res = Db::table('stu_class')->where('sid',$this->clientInfo['uid'])->where('cid',$data['cid'])->delete();

				if ($res == 0) {
					self::returnMsg(401,'退出失败');
				} else {
					self::returnMsg(200,'OK',$res);
				}
			}
			
		}

	}

	public function courseInfo()
	{
		$id = Db::table('token')->where('access_token',$this->clientInfo['access_token'])->value('uid');
		try{
			$cid = Db::table('stu_class')->where('sid',$id)->value('cid');
			if (empty($cid)) {
				self::returnMsg(401,'你未参加任何课程');
			} else {
				$res = Db::table('course')->where('cid',$cid)->select();
				if (empty($res)) {
					self::returnMsg(401,'你参加的班级暂时没有课程');
				} else {
					self::returnMsg(200,'OK',$res);
				}
			}
		} catch (Exception $e) {
			self::returnMsg(401,'Failed',$e);
		}
	}
	/**
	 * 更新学生信息
	 *
	 * @param  \think\Request  $request
	 * @return \think\Response
	 */
	public function update(Request $request)
	{
		$data = input('');
		$this->checkData($data,'update');
		
		$id = $this->clientInfo['uid'];
		try {
			Db::table('student')->where('id',$id)->update($data);
		} catch (Exception $e) {
			self::returnMsg(401,'Insertion failed',$e);
		}
		self::returnMsg(200,'OK',$data);
	}

	//////////////////////////////////工具函数
	//参数验证
	private static function checkData($data = [],$type = '')
	{	
		$validate = new \app\api\validate\Student;
		if(!$validate->check($data,'',$type)){
			return self::returnMsg(401,$validate->getError());
		}
	}
}