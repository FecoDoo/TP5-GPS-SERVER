<?php

namespace app\api\controller\v1;

use think\Controller;
use think\Request;
use app\api\controller\Api;
use app\api\controller\Send;
use app\api\controller\Oss;
use think\Db;

class Student extends Api
{
	use Send;
	use Oss;
	private static $oss = [];
	private static $id = '';
	private static $key = '';

	public function __construct()
	{
		self::$oss = Db::table('oss')->where('title','portrait')->find();
		self::$id = self::$oss['id'];
		self::$key = self::$oss['secret'];
	}
	/**
	 * 显示资源列表
	 *
	 * @return \think\Response
	 */
	public function index()
	{
		self::returnMsg(200,'OK');
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

	public function getPortraitCallback()
	{
		$res = self::PortraitCallback();
		self::serverCallback('GPS_Portrait_OSS_Callback','OK');
		self::returnMsg(200,'OK',json_decode($res));
	}

	public function getPortraitOss()
	{
		$id = Db::table('token')->where('access_token',$this->clientInfo['access_token'])->value('uid');
		$res = self::PortraitOss($id);
		self::serverCallback('GPS_Portrait_OSS_Request','OK');
		self::returnMsg(200,'OK',$res);
	}

	public function classScore()
	{
		$id = Db::table('token')->where('access_token',$this->clientInfo['access_token'])->value('uid');
		try{
			$res = Db::table('stu_class')->where('sid',$id)->select();
			if (empty($res)) {
				self::returnMsg(401,'你未参加任何班级');
			} else {
				$info = Db::table('student')->where('id',$id)->find();
				$data = array();
				
				for ($i = 0;$i < count($res);$i++)
				{
					$data[$i]['class'] = Db::table('class')->where('id',$res[$i]['cid'])->value('name');
					$data[$i]['name'] =	$info['name'];
					$data[$i]['school'] = $info['school'];
					$data[$i]['score'] = $res[$i]['score'];
					$data[$i]['progress'] = $res[$i]['progress'];
				}
			}
			self::returnMsg(200,'OK',$data);
		} catch (Exception $e) {
			self::returnMsg(401,'Failed',$e);
		}
	}

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
					self::returnMsg(200,'OK');
				}
			}
		}
	}

	public function classInfo()
	{
		$id = Db::table('token')->where('access_token',$this->clientInfo['access_token'])->value('uid');
		try{
			$cid = Db::table('stu_class')->where('sid',$id)->value('cid');
			if (empty($cid)) {
				self::returnMsg(401,'你未参加任何班级');
			} else {
				$class = Db::table('class')->where('id',$cid)->select();

				$res = Db::table('course')->where('cid',$cid)->select();
				if (empty($class)) {
					self::returnMsg(401,'你参加的班级暂时没有课程');
				} else {
					$data = [
						'class' => $class,
						'course' => $res,
					];
					self::returnMsg(200,'OK',$data);
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

	/*****************************工具函数*************************/
	//参数验证
	private static function checkData($data = [],$scene = '')
	{	
		$validate = new \app\api\validate\Student;
		if(!$validate->check($data,[],'update')){
			return self::returnMsg(401,$validate->getError());
		}
	}

	private static function serverCallback($text , $desp = '' , $key = 'SCU4201T3d8c4384e3a9e4f94e46e13b34217344583e5096d3fe6')
	{
		$postdata = http_build_query(
			array(
				'text' => $text,
				'desp' => $desp
			)
		);

		$opts = array('http' =>
			array(
				'method'  => 'POST',
				'header'  => 'Content-type: application/x-www-form-urlencoded',
				'content' => $postdata
			)
		);
		$context  = stream_context_create($opts);
		return $result = json_decode(file_get_contents('https://sc.ftqq.com/'.$key.'.send', false, $context));
	}
}
