<?php

namespace App\Http\Controllers\Login;
use App\Http\Controllers\Controller;
use App\Model\UserModel;
use Illuminate\Support\Facades\DB;

class RequestController    extends Controller
{
    /**
     * 注册
     */
    public function request(){
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
    }
}