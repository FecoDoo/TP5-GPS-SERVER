<?php
namespace app\api\validate;

use think\Validate;
/**
 * 生成Student参数验证器
 */
class Student extends Validate
{
	
	protected $rule = [
        'mobile'    =>  'mobile|max:13',
        'password'    =>  'max:12',
        'username'  =>  'max:12',
        'sex'   =>  'max:2',
        'address'   =>  'max:50',
        'idcard'    => 'max:18',
        'email' => 'email|max:50',
        'education' => 'max:12',
        'school'    => 'max:12',
        'major' => 'max:20',
        'practice'  => 'max:50',
        'hobby' => 'max:20',
        'speciality'    => 'max:50',
        'honor' => 'max:50',

        'type' => 'boolean|require',
        'cid' => 'require|max:12',
    ];

    protected $message  =   [
        'mobile.mobile'    => '手机格式错误',
        'mobile.max'    => '手机号最大长度为13',
        'password.max'    => '密码最大长度为12',
        'sex.max' => '性别长度为2',
        'address.max'     => '地址长度为50',
        'idcard.max'   => '身份证长度18',
        'email.email'=>'电子邮件格式错误',
        'education.max'=>'教育背景最大长度12',
        'school.max'=>'学校名称最大长度12',
        'major.max'=>'专业名称最大长度12',
        'practice.max'=>'实习经历最大长度50',
        'hobby.max'=>'爱好最大长度20',
        'speciality.max'=>'特长最大长度50',
        'honor.max'=>'所获荣誉最大长度50',
        'type.require'=>'需要操作类型',
        'cid.max'=>'课程ID最大12',
        'cid.require'=>'需要课程id',
    ];

    protected $currentScene = [
        'update' => ['mobile','password','sex','address','idcard','email','education','school','major','practice','hobby','speciality','
        honor'],
        'class' => ['type','cid'],
        'classInfo' => ['cid'],
    ];
}