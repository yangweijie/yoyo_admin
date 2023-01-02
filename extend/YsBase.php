<?php
use think\facade\Log;

class YsBase{

	public $url     = 'https://ysgate.ysepay.com/openapi'; //域名
    public $testUrl = 'https://appdev.ysepay.com/openapi'; //测试域名

    public $env            = 'prod';
    private $test_password = '123456';
    private $test_cert_is  = '826341457228011';
    private $test_pub_key  = 'U00yIFB1YmxpYyBLZXk6IApYOjdjOWRiNTY5ZjhkYTJiM2I4MWJjY2VjNzBkMzEzMDdmNTdlYjJkNWQwNTdhMGQ3ZjU5ZTFmYzA2YTZiYTA1YzEKWTozMjQyYjM2ZWFmZjU2MDA1MDk5OWUxYWY1ODFiMjdhZjA5ODkxMGU2NTc4YzM1OTVhM2UxYWY4OTQyZTgxMjI5';
    private $test_pri_key = 'U00yIFByaXZhdGUgS2V5OiAKRDoyYTM0YzkyZmNlNTMxYzQzYTM1YThlZjI1YTlmMmVlMTU2NTkyZDRhZmRhNTM2M2FkYjkwNGVkNDc5MTUwYTYyClNNMiBQdWJsaWMgS2V5OiAKWDoxNTYyYjdiZTVmMTM0NWVhMmNiZDM3YTcyYTQ2NDU2NTM5NDFjODZkOTVkOTQ5YWU2NTc0YzBjZmFjNGRmNDc0Clk6OTcwNjkzZmRmMDEzNDJiNmQ4MTBjMmIwNjUwYjU2NWZmZWM2ZTlhMWVhMzRjY2RlNTNhMjhiNTQzZmZjZGM1MA==';

    public function __construct()
    {
    	// $this->
    }

    /**
     * @desc 公共参数
     * @return array
     */
    public function commonParam()
    {
        return [
        	'timeStamp' => date('Y-m-d H:i:s'),
        	'charset'   => 'utf-8',
        	'reqId'     => generate_rand_str(2, 1).date('YmdHis'),
        	// 'reqId'  => Util::randomStr(2).date('YmdHis'),
        	'certId'    => $this->test_cert_is,
        	'version'   => '1.0',
        ];
    }

    /**
     * @desc 请求数据
     * @param $param
     * @param $key
     * @return mixed
     * @throws \Exception
     */
    public function preDoExecute($param, $url)
    {
    	// $this->env      = $param['partner_id'] == 'shanghu_test'? 'test': 'prod';
    	// $password       = $this->env == 'prod'? $param['password']:$this->test_password;
    	$this->env = 'test';
    	$password = $this->test_password;
    	unset($param['password']);

        #1 根据商户号确定接口域名
        $gateway            = $this->env == 'test' ? $this->testUrl : $this->url;
        $url                = $gateway.$url;
        $params             = array_merge($param, $this->commonParam());
        $params['sign']     = md5Sign($params,$key);

        Log::info('银盛参数信息', ['param' => json_encode($params),
        	// 'key'=>$key
        ]);
        // $res = Http::timeout(5)->withBody($json,'application/json')->post($url);
        $res = \util\Http::post($url, $params);
        return json_decode($res, true);
    }
}