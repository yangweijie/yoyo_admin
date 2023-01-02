<?php
namespace app\index\controller;

use app\BaseController;
use Clickfwd\Yoyo\Yoyo as yoyoClass;
use think\facade\View;
use SMEncryptorClass;

class Index extends BaseController
{
    public function index()
    {
        return $this->redirect('admin.php/index/index');
    }

    public function phpinfo()
    {
        phpinfo();
        return '';
    }

    public function hello($name = 'ThinkPHP6')
    {
        return 'hello,' . $name;
    }

    public function yoyo(){
        return (yoyoClass::getInstance())->update();
    }

    public function common_param($cert_id, $method){
        return [
            'method'    => $method,
            'timeStamp' => $this->request->server('request_time'),
            'charset'   => 'utf-8',
            'reqId'     => microtime(true),
            'certId'    => $cert_id,
            'version'   => '1.0',
        ];
    }

    public function sm(){

        // var_dump(get_defined_functions());
        $msg = '这是测试';

        // $iv = '1234567812345678'; //
        $iv = '1234567812345678'; //没有设置默认为空的操作：如需为空请设置1234567812345678

        // $pub_key    = 'U00yIFB1YmxpYyBLZXk6IApYOjdjOWRiNTY5ZjhkYTJiM2I4MWJjY2VjNzBkMzEzMDdmNTdlYjJkNWQwNTdhMGQ3ZjU5ZTFmYzA2YTZiYTA1YzEKWTozMjQyYjM2ZWFmZjU2MDA1MDk5OWUxYWY1ODFiMjdhZjA5ODkxMGU2NTc4YzM1OTVhM2UxYWY4OTQyZTgxMjI5';
        // $pri_key    = 'U00yIFByaXZhdGUgS2V5OiAKRDoyYTM0YzkyZmNlNTMxYzQzYTM1YThlZjI1YTlmMmVlMTU2NTkyZDRhZmRhNTM2M2FkYjkwNGVkNDc5MTUwYTYyClNNMiBQdWJsaWMgS2V5OiAKWDoxNTYyYjdiZTVmMTM0NWVhMmNiZDM3YTcyYTQ2NDU2NTM5NDFjODZkOTVkOTQ5YWU2NTc0YzBjZmFjNGRmNDc0Clk6OTcwNjkzZmRmMDEzNDJiNmQ4MTBjMmIwNjUwYjU2NWZmZWM2ZTlhMWVhMzRjY2RlNTNhMjhiNTQzZmZjZGM1MA==';
        // $pri_key = 'MIGHAgEAMBMGByqGSM49AgEGCCqBHM9VAYItBG0wawIBAQIgKjTJL85THEOjWo7yWp8u4VZZLUr9pTY625BO1HkVCmKhRANCAAQVYre+XxNF6iy9N6cqRkVlOUHIbZXZSa5ldMDPrE30dJcGk/3wE0K22BDCsGULVl/+xumh6jTM3lOii1Q//NxQ'; 
        // $pwd        = 'a1234567';
        // sm2_key_pair($pub_key, $pri_key);
        $pub_key = base64_decode('MIIDQDCCAuWgAwIBAgIFRFQgEHkwDAYIKoEcz1UBg3UFADBcMQswCQYDVQQGEwJDTjEwMC4GA1UECgwnQ2hpbmEgRmluYW5jaWFsIENlcnRpZmljYXRpb24gQXV0aG9yaXR5MRswGQYDVQQDDBJDRkNBIEFDUyBTTTIgT0NBMzEwHhcNMjExMDE4MDczNzQwWhcNMjYxMDE4MDczNzQwWjB0MQswCQYDVQQGEwJDTjETMBEGA1UECgwKQ0ZDQSBPQ0EzMTEPMA0GA1UECwwGeXNlcGF5MRkwFwYDVQQLDBBPcmdhbml6YXRpb25hbC0xMSQwIgYDVQQDDBswNTFAeXNlcGF5X3NtMkB5c2VwYXlfc20yQDQwWTATBgcqhkjOPQIBBggqgRzPVQGCLQNCAAR8nbVp+NorO4G8zscNMTB/V+stXQV6DX9Z4fwGproFwTJCs26v9WAFCZnhr1gbJ68JiRDmV4w1laPhr4lC6BIpo4IBeDCCAXQwbAYIKwYBBQUHAQEEYDBeMCgGCCsGAQUFBzABhhxodHRwOi8vb2NzcC5jZmNhLmNvbS5jbi9vY3NwMDIGCCsGAQUFBzAChiZodHRwOi8vY3JsLmNmY2EuY29tLmNuL29jYTMxL29jYTMxLmNlcjAfBgNVHSMEGDAWgBQI2NEmxEh9nOysmOnxf2K5gM6pRTAMBgNVHRMBAf8EAjAAMEgGA1UdIARBMD8wPQYIYIEchu8qAQQwMTAvBggrBgEFBQcCARYjaHR0cDovL3d3dy5jZmNhLmNvbS5jbi91cy91cy0xNC5odG0wPQYDVR0fBDYwNDAyoDCgLoYsaHR0cDovL2NybC5jZmNhLmNvbS5jbi9vY2EzMS9TTTIvY3JsMTYxNS5jcmwwDgYDVR0PAQH/BAQDAgbAMB0GA1UdDgQWBBR4jz33XMldfoLbC6l7VJ8nlN67ITAdBgNVHSUEFjAUBggrBgEFBQcDAgYIKwYBBQUHAwQwDAYIKoEcz1UBg3UFAANHADBEAiB5DkPSx9dxdWCAl9C5fgrbiTy/8vTTd0eCKoTbXztTuwIgRzF+G2H2O4P3ZXioIkICUA3ENH9tzMe3fL7wdwPKKNw=');
        $pri_key = base64_decode('TUlJRHJ3SUJBVEJIQmdvcWdSelBWUVlCQkFJQkJnY3FnUnpQVlFGb0JEQjBlNExMeWdPUEtZQ1krd0dUNmZUL0FCRElTZUVJM1NEVg0KdEZtanlOWEtTYkZHbnphTGNINDlIZG1nUDZmVWxjTXdnZ05mQmdvcWdSelBWUVlCQkFJQkJJSURUekNDQTBzd2dnTHdvQU1DQVFJQw0KQlVVakExWURNQXdHQ0NxQkhNOVZBWU4xQlFBd1hERUxNQWtHQTFVRUJoTUNRMDR4TURBdUJnTlZCQW9NSjBOb2FXNWhJRVpwYm1GdQ0KWTJsaGJDQkRaWEowYVdacFkyRjBhVzl1SUVGMWRHaHZjbWwwZVRFYk1Ca0dBMVVFQXd3U1EwWkRRU0JCUTFNZ1UwMHlJRTlEUVRNeA0KTUI0WERUSXlNRFF3T0RBM016VXlPRm9YRFRJM01EUXdPREEzTXpVeU9Gb3dmekVMTUFrR0ExVUVCaE1DUTA0eEV6QVJCZ05WQkFvTQ0KQ2tOR1EwRWdUME5CTXpFeER6QU5CZ05WQkFzTUJubHpaWEJoZVRFWk1CY0dBMVVFQ3d3UVQzSm5ZVzVwZW1GMGFXOXVZV3d0TVRFdg0KTUMwR0ExVUVBd3dtTURVeFFEZ3lOak0wTVRRMU56SXlPREF4TVVBNE9EVXlNVEF4TkRVMU5UQXhUVVEyUURFd1dUQVRCZ2NxaGtqTw0KUFFJQkJnZ3FnUnpQVlFHQ0xRTkNBQVFWWXJlK1h4TkY2aXk5TjZjcVJrVmxPVUhJYlpYWlNhNWxkTURQckUzMGRKY0drLzN3RTBLMg0KMkJEQ3NHVUxWbC8reHVtaDZqVE0zbE9paTFRLy9OeFFvNElCZURDQ0FYUXdiQVlJS3dZQkJRVUhBUUVFWURCZU1DZ0dDQ3NHQVFVRg0KQnpBQmhoeG9kSFJ3T2k4dmIyTnpjQzVqWm1OaExtTnZiUzVqYmk5dlkzTndNRElHQ0NzR0FRVUZCekFDaGlab2RIUndPaTh2WTNKcw0KTG1ObVkyRXVZMjl0TG1OdUwyOWpZVE14TDI5allUTXhMbU5sY2pBZkJnTlZIU01FR0RBV2dCUUkyTkVteEVoOW5PeXNtT254ZjJLNQ0KZ002cFJUQU1CZ05WSFJNQkFmOEVBakFBTUVnR0ExVWRJQVJCTUQ4d1BRWUlZSUVjaHU4cUFRUXdNVEF2QmdnckJnRUZCUWNDQVJZag0KYUhSMGNEb3ZMM2QzZHk1alptTmhMbU52YlM1amJpOTFjeTkxY3kweE5DNW9kRzB3UFFZRFZSMGZCRFl3TkRBeW9EQ2dMb1lzYUhSMA0KY0RvdkwyTnliQzVqWm1OaExtTnZiUzVqYmk5dlkyRXpNUzlUVFRJdlkzSnNNakEyT0M1amNtd3dEZ1lEVlIwUEFRSC9CQVFEQWdiQQ0KTUIwR0ExVWREZ1FXQkJRbmFrVjF3Sld0WmplMjZjSjVCWERBaDRPamVUQWRCZ05WSFNVRUZqQVVCZ2dyQmdFRkJRY0RBZ1lJS3dZQg0KQlFVSEF3UXdEQVlJS29FY3oxVUJnM1VGQUFOSEFEQkVBaUFpZEFJckZtK3JyRmhFYnJwdHFiM0Q4bXhPWW5meVJZdUlmeEVSWkJQTw0KcmdJZ0FqemR6a0VSTjVscTJXRkZaTjM4MWE1Z1FTenJtcW12QzRoL0k2L2pXcDA9');
        // trace($pub_key);
        dump('公钥base64', base64_encode($pub_key));
        dump('私钥base64', base64_encode($pri_key));
        // $pri_key = unpack("H*", $pri_key)[1];
        #公钥:BHSAPGXtrHNxqJ3/b0+eNu2mdO0mpDfTGNJUMoEWpNpSL53Dw+YM/B/QT5OoLm4xQtw0hZY5wlWTR+cD629Grek=
        #私钥:++BuzKd1mPa0RXAJcY6DHDq9SUzo3T6/engbKReQRqI=

        $ret = sm2_sign($msg, $signature, $pri_key, $iv);
        dump($ret);
        // dump('signature', $signature);
        dump('签名：', base64_encode($signature));

        #私钥签名:+YHNtKkXbsRSs2nk5amd/YNqsiH8Kyr+oyLVVzuvRl+lqb40uzPxjsRo9QTYw7kZdWSfvM5lbxDMfF0cugQNfQ==

        $flag = sm2_sign_verify($msg, $signature, $pub_key, $iv);
        dump('公钥验签:', $flag);

        #公钥验签:0

        sm2_encrypt($msg, $encrypt, $pub_key);
        dump('公钥加密后', $encrypt);
        // base64_encode($encrypt);
        dump('公钥加密后base64', base64_encode($encrypt));

        #公钥加密:BBdm04Uh5EgzYKG3Ff8rBFJQZxRSXnrh9/WDZxS6PmzfnTDz0O0C115BPxMDfBNnOK5Ixs9kHTJPNSDoiHoiEmrnuotKN53rxnJtNd3MTbRjJOQ0sas9Kdktl1eHzj2/eseNaGh0LHZIOrBxAQ==
        sm2_decrypt($encrypt, $string, $pri_key);
        dump('解密后', $string);

        #私钥解密:这是测试
        return '';
    }

    public function sm2(){
        // X-SPDB-ClientID-Secret
        //加解密样例仅供参考，秘钥有过期时间，建议在生产环境不要将秘钥硬编码在代码中
        $secret = generate_rand_str(32, 0);
        //加解密样例仅供参考，秘钥有过期时间，建议在生产环境不要将秘钥硬编码在代码中
        // $privateKey = realpath(root_path().'/extend/cert/826341457228011.sm2');
        //浦发SM2公钥
        //加解密样例仅供参考，秘钥有过期时间，建议在生产环境不要将秘钥硬编码在代码中
        // $spdb_publicKey = realpath(root_path().'/extend/cert/sm2businessgate.cer');
        $publicKey = 'U00yIFB1YmxpYyBLZXk6IApYOjdjOWRiNTY5ZjhkYTJiM2I4MWJjY2VjNzBkMzEzMDdmNTdlYjJkNWQwNTdhMGQ3ZjU5ZTFmYzA2YTZiYTA1YzEKWTozMjQyYjM2ZWFmZjU2MDA1MDk5OWUxYWY1ODFiMjdhZjA5ODkxMGU2NTc4YzM1OTVhM2UxYWY4OTQyZTgxMjI5';

        $privateKey    = 'U00yIFByaXZhdGUgS2V5OiAKRDoyYTM0YzkyZmNlNTMxYzQzYTM1YThlZjI1YTlmMmVlMTU2NTkyZDRhZmRhNTM2M2FkYjkwNGVkNDc5MTUwYTYyClNNMiBQdWJsaWMgS2V5OiAKWDoxNTYyYjdiZTVmMTM0NWVhMmNiZDM3YTcyYTQ2NDU2NTM5NDFjODZkOTVkOTQ5YWU2NTc0YzBjZmFjNGRmNDc0Clk6OTcwNjkzZmRmMDEzNDJiNmQ4MTBjMmIwNjUwYjU2NWZmZWM2ZTlhMWVhMzRjY2RlNTNhMjhiNTQzZmZjZGM1MA==';

        $cert_id        = '826440345119153';
        $url            = 'https://appdev.ysepay.com/openapi/aggregation/scan/mccQuery';
        $param = [
            'mccCd'    => 5099,
            'mercType' => '',
        ];
        //请求报文体
        $body_data = array_merge($this->common_param($cert_id, 'aggregation.scan.mccQuery'), [
            'param' => $param,
        ]);
        //请求方式  get 或 post
        $method = 'post';
        //防重放参数,默认为false
        $forbidden = false;
        //创建类对象
        $SMEncry = new SMEncryptorClass();
        //$clientid, $secret, $privateKey, $spdb_publicKey, $url, $body_data, $method,$forbidden
        $info = $SMEncry->SmOperator($secret, $privateKey, $publicKey, $url, $body_data, $method, $forbidden);
        $jsonInfo = json_decode($info);
        dump($jsonInfo);
        // echo "加密报文体:" . $jsonInfo->bodyEncryBase64; //加密报文体
        // echo "<br/>";
        // echo "解密报文体:" . $jsonInfo->bodyDecry; //解密报文体
        // echo "<br/>";
        // echo "签名原文：" . $jsonInfo->new_body_data; //签名原文（可能包含时间戳）
        // echo "<br/>";
        // echo "signature:" . $jsonInfo->signature; //签名signature
        // echo "<br/>";
        // echo "响应signature:" . $jsonInfo->resSignature; //响应密文
        // echo "<br/>";
        // echo "加密响应报文:" . $jsonInfo->resbody; //加密响应明文
        // echo "<br/>";
        // echo "解密响应报文:" . $jsonInfo->resBodyData; //解密响应明文
        // echo "<br/>";
        // echo "验签结果:" . $jsonInfo->signResult;//验签结果，1表示成功，其他表示失败
    }
}
