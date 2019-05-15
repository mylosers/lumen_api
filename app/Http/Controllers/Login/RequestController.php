<?php

namespace App\Http\Controllers\Login;
use App\Http\Controllers\Controller;

class RequestController    extends Controller
{
    /**
     * 注册
     */
    /*public function request(){
        //解密
        $data = file_get_contents('php://input');
        $enc_data=base64_decode($data);
        $pk=openssl_get_publickey('file://keys/public.pem');
        openssl_public_decrypt($enc_data,$dec_data,$pk);
        $data=json_decode($dec_data,true);
        $data=[
            'name'=>$data['name'],
            'email'=>$data['email'],
            'pwd'=>password_hash($data['pwd'],PASSWORD_BCRYPT),
        'add_time'=>time()
        ];
        $info=DB::table('user')->insert($data);
        if($info){
            $res=[
                'error'=>0,
                'msg'=>'注册成功'
            ];
        }else{
            $res=[
                'error'=>50003,
                'msg'=>'注册失败'
            ];
        }
        return json_encode($res,JSON_UNESCAPED_UNICODE);
    }*/

    /**
     * api注册接口
     */
    public function requestAdd(){
        $name=$_POST['name'];
        $email=$_POST['email'];
        $pwd=$_POST['pwd'];
        $data=[
            'name'=>$name,
            'email'=>$email,
            'pwd'=>$pwd
        ];
        //加密
        $json_str=json_encode($data,256);
//        $k=openssl_pkey_get_private('file://keys/private.pem');
//        //加密
//        openssl_private_encrypt($json_str,$enc_data,$k);
//        $b64=base64_encode($enc_data);
        $url = 'http://client.myloser.club/requestInfo';
        //初始化URL
        $ch = curl_init();
        //设置抓取的url
        curl_setopt($ch, CURLOPT_URL, $url);
        //设置post方式提交
        curl_setopt($ch, CURLOPT_POST, 1);
        //传值
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_str);
        //返回结果不输入
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //响应头
        curl_setopt($ch,CURLOPT_HTTPHEADER,['Content-type:text/plain']);
        //获取抛出错误
        $num=curl_errno($ch);
        if($num>0){
            echo 'curl错误码：'.$num;exit;
        }
        //发起请求
        curl_exec($ch);
        //关闭并释放资源
        curl_close($ch);

    }
}