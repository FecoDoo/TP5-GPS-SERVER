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
		$return['data'] = explode('&', $data);
        self::serverCallback('Callback', implode('&',$return['data']));
		exit(json_encode($return,JSON_UNESCAPED_UNICODE));
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
