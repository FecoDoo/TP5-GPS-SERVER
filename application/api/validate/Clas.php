<?php
namespace app\api\validate;

use think\Validate;
/**
 * 生成Student参数验证器
 */
class Clas extends Validate
{
	
	protected $rule = [
        'cid' => 'require|max:12',
    ];

    protected $message  =   [
        'cid.max'=>'课程ID最大12',
        'cid.require'=>'需要课程id',
    ];

    protected $currentScene = [
        'info' => ['cid'],
    ];
}