<?php
namespace app\index\controller;
use think\Request;

class Index
{
    public function index()
    {
        return 'ThinkPHP Index Test Page';
    }

    public function callback()
    {
        $request = request();
        $this->send($request->query());
    }

    protected function send($data)
    {
    	$return['code'] = 200;
		$return['message'] = 'OK';
		$return['data'] = is_array($data) ? $data : ['info'=>explode('&', $data)];
		exit(json_encode($return,JSON_UNESCAPED_UNICODE));
    }
}
