<?php

use think\facade\Log;
use Mdanter\Ecc\Serializer\PublicKey\DerPublicKeySerializer;
use Mdanter\Ecc\Serializer\PublicKey\PemPublicKeySerializer;
use Mdanter\Ecc\EccFactory;
use util\Aes;
use sm\MySm2;
use sm\MySm3;
use sm\MySm4;

class SMEncryptorClass
{
    public function SmOperator($secret, $privateKeyFile, $publicKeyFile, $url, $body_data, $method)
    {

        $sm2       = new MySm2('hex');
        $sm3       = new MySm3();
        $sm4       = new MySm4();
        $this->sm2 = $sm2;
        $this->sm3 = $sm3;
        $this->sm4 = $sm4;

        $body_param = $body_data['param'];
        unset($body_data['param']);

        $reqMap = $body_data;

    
        /** 2、生成对业务参数加密的密钥*/

        $sha256Key = hash("sha256", $secret);
        $sm3Key    = $sm3->digest($sha256Key, 1);
        $md5Key    = md5($sm3Key);
        $pubKey    = strtolower($publicKeyFile);
        $pubKey    = $publicKeyFile;
        $pri_key   = $privateKeyFile;

        /** 3、使用银盛公钥对密钥key进行加密，得到加密的key并设置到请求参数check中。publicKeyFile为存放银盛公钥的地址*/
        sm2_encrypt($md5Key, $encrypt, $pubKey);
        // $check           = $sm4->setKey($pubKey)->encryptData($md5Key);
        $reqMap['check'] = $encrypt;

        //echo "加密报文体:" . $bodyEncry;
        //echo "<br/>";
        // $bodyDecry = $sm4->setKey(strtolower($pubKey))->decryptData($encrypt);

        //echo "解密报文体:" . $bodyDecry;
        //echo "<br/>";

        /** 4、封装业务参数,具体参数见文档，这里只是举例子*/

        $bizContentMap = $body_param;

        /** 5、使用生成的密钥key对业务参数进行加密，并将加密后的业务参数放入请求参数bizContent中*/
        $aes                  = new Aes($md5Key);
        $bizEncryStr          = $aes->encode(json_encode($bizContentMap));
        $bizBase64            = base64_encode($bizEncryStr);
        $reqMap['bizContent'] = $bizBase64;

        /** 6、将请求参数进行sort排序，生成拼接的字符床，并使用接入方私钥对请求参数进行加签，并将加签的值存入请求参数sign中*/
        //6.1 排序生成拼接字符串
        $userId       = null;
        $reqMapToSign = $reqMap;
        unset($reqMapToSign['sign']);
        ksort($reqMapToSign);
        $iv = '1234567812345678';
         //6.2使用接入方私钥对排序的请求参数加签，并存放到请求参数里面.privateKeyFile:私钥地址，prvatePassword:私钥密钥
        sm2_sign(http_build_query($reqMapToSign), $sign, $privateKeyFile, $iv);

        // $sign           = $sm2->doSignOutKey(http_build_query($reqMapToSign), $privateKeyFile, $userId);
        $signature      = base64_encode($sign);
        $reqMap['sign'] = $signature;

        //发送http请求
        //存储返回的加密密文和返回的X-SPDB-SIGNATURE
        $resSignature = "";
        $resbody      = "";
        $start_time   = microtime(true);
        $resu         = $this->http_Requset($url, $reqMap, $method);
        $cha_time     = microtime(true)-$start_time;
        Log::record('调用银盛接口花费时间'.$cha_time);
        Log::record('调用银盛接口返回数据'.$resu);
        $headArr = explode("\r\n", $resu);
        for ($i = 0; $i < count($headArr); $i++) {
            if (strpos($headArr[$i], "X-SPDB-SIGNATURE:") !== false) {
                $resSignature = trim(substr($headArr[$i], 18));
            }
            if (($i + 1) == count($headArr)) {
                $resbody = trim($headArr[$i]);
            }
        }
        sm2_decrypt($resbody, $resBodyData, $pri_key);
        // $resBodyData = $sm4->setKey($md5Key)->decryptData(strtolower(base64_decode($resbody)));
        $pos = strrpos($resBodyData,'}');
        $resBodyData = substr($resBodyData,0,$pos+1);
        Log::record('明文响应报文'.trim($resBodyData));

        //验签
        //var_dump($sm2->verifySign(base64_encode(sha1(trim($resBodyData), true)), base64_decode($resSignature), $spdb_publicKey, $userId));
        $result = sm2_sign_verify(trim($resBodyData), $signature, $pubKey, $iv);
        $returnInfo = array(
            'bodyEncryBase64' => $encrypt,//加密报文体
            'bodyDecry'       => $md5Key,//解密报文体
            'new_body_data'   => $reqMapToSign,//签名原文（可能包含时间戳）
            'signature'       => $signature,//签名signature
            'resSignature'    => $resSignature,//响应resSignature
            'resbody'         => $resbody,//响应密文
            'resBodyData'     => $resBodyData,//响应明文
            'signResult'      => $result//验签结果，1表示成功，其他表示失败
        );
        return json_encode($returnInfo);
    }


    public function outPubKeyFromFile($pubFile){
        $keyData       = file_get_contents( $pubFile );
        // return openssl_pkey_get_public($keyData);
        $adapter       = EccFactory::getAdapter(false);
        $derSerializer = new DerPublicKeySerializer( $adapter );
        $pemSerializer = new PemPublicKeySerializer( $derSerializer );
        $key           = $pemSerializer->parse( $keyData );
        return         $key;
    }

    //发送http请求方法
    public function http_Requset($url, $encryBody, $reqMethod)
    {
        $curl      = curl_init(); //启动一个CURL会话
        $headers[] = "Content-type:application/json;charset=utf-8";
        //需要防重放的时候放开，根据forbidden开启防重放，默认为false
        //$headers[] = "X-SPDB-Timestamp:".$timestamp;
        //$headers[] = "X-SPDB-Nonce:".$nonce;
        if ($reqMethod == "post" || $reqMethod == "POST") {
            // 为true表示用post请求
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $encryBody);
        }
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_NOBODY, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);  // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $tmpInfo = curl_exec($curl);
        //关闭URL请求
        curl_close($curl);
        //echo $tmpInfo;
        return $tmpInfo;
    }

    //解密和验签
    public function decryptData($data, $secret)
    {

        $sm3 = $this->sm3;
        $sm4 = $this->sm4;

        //echo "原报文体:" . $body_data;
        //echo "<br/>";
        $sha256Key   = hash("sha256", $secret);
        $sm3Key      = $sm3->digest($sha256Key, 1);
        $md5Key      = md5($sm3Key);
        $resBodyData = $sm4->setKey($md5Key)->decryptData(strtolower(base64_decode($data)));
        return $resBodyData;
    }
}
