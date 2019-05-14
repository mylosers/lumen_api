<?php

namespace App\Http\Controllers\Login;
use App\Http\Controllers\Controller;
use App\Model\UserModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;
class LoginController    extends Controller
{
    /**
     * 登陆
     */
    public function login(){
//        header("Access-Control-Allow-Origin: *");

      /*  $callback = $_GET["email"];
        $a = array(
            'code'=>'CA1998',
            'price'=>'6000',
            'tickets'=>20,
            'func'=>$callback,
        );
        $result = json_encode($a);
        echo "person($result)";die;*/
        //解密
        $data = file_get_contents('php://input');
        $enc_data=base64_decode($data);
        $pk=openssl_get_publickey('file://keys/public.pem');
        openssl_public_decrypt($enc_data,$dec_data,$pk);
        $data=json_decode($dec_data,true);
        $info_table=DB::table('user')->where(['email'=>$data['email']])->first();
        if($info_table){
            //TODO 登陆逻辑
            //判断密码是否正确
            $info=password_verify($data['pwd'],$info_table->pwd);
            if($info==true){
                $token=$this->token($info_table->id);//生成token
                /*$key='login_token';
                Redis::setex($key,3600,$token.','.$uid->id);
                $val=Redis::get($key); //查询key值中的val值
                $arr=explode(',',$val); //根据，切割字符串为数组 explode*/
                $redis_token_key = 'login_token:id:'.$info_table->id;
                Redis::set($redis_token_key,$token);
                Redis::expire($redis_token_key,604800);
                setcookie('token',Str::random(6),time()+50,'/','client.myloser.club',false,true);
                setcookie('id',999,time()+50,'/','client.myloser.club',false,true);
                $res=[
                    'error'=>0,
                    'msg'=>'登陆成功',
                    'data'  => [
                        'token' => $token
                    ]
                ];
                die(json_encode($res,JSON_UNESCAPED_UNICODE));
            }else{
                //密码不正确
                $res=[
                    'error'=>50005,
                    'msg'=>'密码不正确'
                ];
                die(json_encode($res,JSON_UNESCAPED_UNICODE));
            }
        }else{
            //查无此人
            $res=[
                'error'=>50004,
                'msg'=>'没有此用户'
            ];
            die(json_encode($res,JSON_UNESCAPED_UNICODE));
        }
    }
    /**
     * token
     */
    public function token($id){
        return substr(sha1($id.time() .Str::random(10) ),5,15);
    }
}