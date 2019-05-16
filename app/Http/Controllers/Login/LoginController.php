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
    public function login()
    {
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
        $enc_data = base64_decode($data);
        $pk = openssl_get_publickey('file://keys/public.pem');
        openssl_public_decrypt($enc_data, $dec_data, $pk);
        $data = json_decode($dec_data, true);
        $info_table = DB::table('user')->where(['email' => $data['email']])->first();
        if ($info_table) {
            //TODO 登陆逻辑
            //判断密码是否正确
            $info = password_verify($data['pwd'], $info_table->pwd);
            if ($info == true) {
                $token = $this->token($info_table->id);//生成token
                /*$key='login_token';
                Redis::setex($key,3600,$token.','.$uid->id);
                $val=Redis::get($key); //查询key值中的val值
                $arr=explode(',',$val); //根据，切割字符串为数组 explode*/
                $redis_token_key = 'login_token:id:' . $info_table->id;
                Redis::set($redis_token_key, $token);
                Redis::expire($redis_token_key, 604800);
                /*setcookie('token',Str::random(6),time()+50,'/','client.myloser.club',false,true);
                setcookie('id',999,time()+50,'/','client.myloser.club',false,true);*/
                $res = [
                    'error' => 0,
                    'msg' => '登陆成功',
                    'data' => [
                        'token' => $token
                    ]
                ];
                die(json_encode($res, JSON_UNESCAPED_UNICODE));
            } else {
                //密码不正确
                $res = [
                    'error' => 50005,
                    'msg' => '密码不正确'
                ];
                die(json_encode($res, JSON_UNESCAPED_UNICODE));
            }
        } else {
            //查无此人
            $res = [
                'error' => 50004,
                'msg' => '没有此用户'
            ];
            die(json_encode($res, JSON_UNESCAPED_UNICODE));
        }
    }

    /**
     * token
     */
    public function token($id)
    {
        return substr(sha1($id . time() . Str::random(10)), 5, 15);
    }

    /**
     * 登陆2
     */
    public function loginTwo()
    {
//        header("Access-Control-Allow-Origin: *");
        $email = $_POST['email'];
        $pwd = $_POST['pwd'];
        if ($email == "") {
            $res = [
                'error' => 50004,
                'msg' => '用户名必填'
            ];
            die(json_encode($res, JSON_UNESCAPED_UNICODE));
        } else if ($pwd == "") {
            $res = [
                'error' => 50003,
                'msg' => '密码必填'
            ];
            die(json_encode($res, JSON_UNESCAPED_UNICODE));
        }

        $url = 'http://api.myloser.club/login';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS,$b64);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Conten-Type:text/plain']);
        $info=curl_exec($curl);
        $error=curl_errno($curl);
        if($error>0){
            echo "CURL 错误码：".$error;exit;
        }
        curl_close($curl);

        $info_table = DB::table('user')->where(['email' => $email])->first();
        if ($info_table) {
            //TODO 登陆逻辑
            //判断密码是否正确
            $info = password_verify($pwd, $info_table->pwd);
            if ($info == true) {
                $token = $this->token($info_table->id);//生成token
                /*$key='login_token';
                Redis::setex($key,3600,$token.','.$uid->id);
                $val=Redis::get($key); //查询key值中的val值
                $arr=explode(',',$val); //根据，切割字符串为数组 explode*/
                $redis_token_key = 'login_token;id:'.$info_table->id;
                Redis::set($redis_token_key, $token);
                Redis::expire($redis_token_key, 604800);
                /*setcookie('token',Str::random(6),time()+50,'/','client.myloser.club',false,true);
                setcookie('id',999,time()+50,'/','client.myloser.club',false,true);*/
                $res = [
                    'error' => 0,
                    'msg' => '登陆成功',
                    'data' => [
                        'token' => $token,
                        'id'=>$info_table->id
                    ]
                ];
                die(json_encode($res, JSON_UNESCAPED_UNICODE));
            }else {
                //密码不正确
                $res = [
                    'error' => 50005,
                    'msg' => '密码不正确'
                ];
                die(json_encode($res, JSON_UNESCAPED_UNICODE));
            }
        } else {
            //查无此人
            $res = [
                'error' => 50004,
                'msg' => '没有此用户'
            ];
            die(json_encode($res, JSON_UNESCAPED_UNICODE));
        }
    }

    public function check(){
        echo 1;
    }

    /**
     * 登陆api接口
     */
    public function loginAdd(){
        $email=$_POST['email'];
        $pwd=$_POST['pwd'];
        $data=[
            'email'=>$email,
            'pwd'=>$pwd
        ];
        $json_str=json_encode($data,256);
        $url = 'http://client.myloser.club/loginInfo';
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