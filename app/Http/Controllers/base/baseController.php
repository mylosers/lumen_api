<?php

namespace App\Http\Controllers\base;
use App\Http\Controllers\Controller;
class baseController    extends Controller
{
    public function base(){
        $data = json_decode(file_get_contents('php://input'), true);
        $str=$data['foo'];
        $key = "wenzi";

        //解密
        $strArr = str_split($str,2);
        foreach (str_split($key) as $k => $v) {
            if($strArr[$k][1] === $v){
                $strArr[$k]=$strArr[$k][0];
            }
        }
        $newInfo = join("",$strArr);
        $newInfo = base64_decode($newInfo);

        //记录日志
        $log_str = date('Y-m-d H:i:s') . "\n" . $data['foo'] ."\n".$newInfo. "\n<<<<<<<";
        file_put_contents('logs/wx_event.log', $log_str, FILE_APPEND);

        //TODO 业务逻辑
    }

    /**
     *非对称解密
     */
    public function rsaNo(){
        //解密
        $data = json_decode(file_get_contents('php://input'), true);
        $enc_data=base64_decode($data['foo']);
        $pk=openssl_get_publickey('file://'.storage_path('app/keys/public.pem'));
        openssl_public_decrypt($enc_data,$dec_data,$pk);

        //记录日志
        $log_str = date('Y-m-d H:i:s')  ."\n".$dec_data. "\n<<<<<<<";
        file_put_contents('logs/wx_event.log', $log_str, FILE_APPEND);

        //TODO 业务逻辑
    }

    /**
     * 签名验证
     */
    public function sign(){
        //解密
        $data = json_decode(file_get_contents('php://input'), true);
        dump($data);
        $sign=$_GET['sign'];echo "<hr>";
        echo $enc_data=base64_decode($sign);echo "<hr>";
        $pk=openssl_get_publickey('file://'.storage_path('app/keys/public.pem'));
//        var_dump($pk);die;
        $info=openssl_verify(json_encode($data),$enc_data,$pk);
        dump($info);

        //TODO 业务逻辑
    }
}
