<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Jwt
{
    private $alg = 'sha256';

    private $secret = "ffdsfsd@4_45";

    // protected $CI;//ci实例

    // public function __construct(){
    //     $this->CI =& get_instance();
    // }


    /**
     * Payload 部分也是一个 JSON 对象，用来存放实际需要传递的数据。JWT 规定了7个官方字段，供选用，这里可以存放私有信息，比如uid
     * @param $uid int 用户id
     * @return mixed
     */
    public function getPayload($uid)
    {
        $payload = [
            
            'exp' => time() + 86400*7, //过期时间            
            //'iat' => time(), //签发时间           
            'uid' => $uid, //私有信息，uid
           
        ];


        return $this->base64urlEncode(json_encode($payload, JSON_UNESCAPED_UNICODE));
    }

    /**
     * 生成token,假设现在payload里面只存一个uid
     * @param $uid int
     * @return string
     */
    public function getToken($uid)
    {   
        if(!$uid){
            return '';
        }        
        
        $payload = $this->getPayload($uid);
       
        $raw   = $payload;
        $token = $raw . '.' . hash_hmac($this->alg, $raw, $this->secret);

        return $token;
    }


    /**
     * 解密校验token,成功的话返回uid
     * @param $token
     * @return mixed
     */
    public function verifyToken($token)
    {
        if (!$token) {
            return false;
        }
        $tokenArr = explode('.', $token);
        
        if (count($tokenArr) != 2) {
            return false;
        }
        
        $payload   = $tokenArr[0];
        $signature = $tokenArr[1];

        $payloadArr = json_decode($this->base64urlDecode($payload), true);

        if (!$payloadArr) {
            return false;
        }

        //已过期
        if (isset($payloadArr['exp']) && $payloadArr['exp'] < time()) {
            return false;
        }

        $expected = hash_hmac($this->alg, $payload, $this->secret);

        //签名不对
        if ($expected !== $signature) {
            return false;
        }

        return $payloadArr['uid'];
    }

    /**
     * 安全的base64 url编码
     * @param $data
     * @return string
     */
    private function base64urlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * 安全的base64 url解码
     * @param $data
     * @return bool|string
     */
    private function base64urlDecode($data)
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
}
