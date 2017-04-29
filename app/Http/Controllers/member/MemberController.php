<?php

namespace App\Http\Controllers\member;


use Illuminate\Support\Facades\Mail;
use App\Http\Requests;
use App\Models\M3Result;
use App\Models\Member;
use App\Models\TempPhone;
use App\Models\TempEmail;
use App\Models\M3Email;
use Illuminate\http\Request;
use App\Tool\UUID;
use App\Http\Controllers\Controller;

class MemberController extends Controller
{




    /**
     * 新用户登录
     */
    public function getLogin(Request $request)
    {
        $return_url = $request->input('return_url', '');
        return view('user/login')->with('return_url', urldecode( $return_url ));

    }





    /**
     * 用户登录验证
     */
    public function doLogin(Request $request)
    {

        $username = $request->input('username','');
        $password = $request->input('password','');
        $validate_code = $request->input('validate_code','');

        $m3_result = new M3Result();

        $validate_code_session = $request->session()->get('validate_code');

        if( $validate_code != $validate_code_session ){

            $m3_result->status = 1;
            $m3_result->message = '验证码不正确';
            return $m3_result->toJson();
        }

        if(strpos( $username,'@') == true){
            $member = Member::where('email',$username)->first();
        }else{
            $member = Member::where('phone',$password)->first();
        }

        if( $member == null ){

            $m3_result->status = 1;
            $m3_result->message = '该用户不存在';
            return $m3_result->toJson();
        } else {

            if(md5('bk'+$password) != $member->password){
                $m3_result->status = 1;
                $m3_result->message = '密码不正确';
                return $m3_result->toJson();
            }
        }

        //写入session
        $request->session()->put('member',$member);

        //登录成功
        $m3_result->status = 0;
        $m3_result->message = '登录成功';
        return $m3_result->toJson();
    }



    /**
     * 注册页面显示
     */
    public function getRegister()
    {

        return view('user/register');

    }




    /**
     * 新用户注册
     */
    public function doReg(Request $request)
    {

        $email = $request->input('email','');
        $phone = $request->input('phone','');
        $password = $request->input('password','');
        $confirm = $request->input('confirm','');
        $phone_code = $request->input('phone_code','');
        $validate_code = $request->input('validate_code','');

        $m3_result = new M3Result();

        if( $email == '' && $phone == '' ){

            $m3_result->status = 1;
            $m3_result->message = '手机号或邮箱不能为空';
            return $m3_result->toJson();
        }

        if( $password == '' && strlen($password) < 6 ){

            $m3_result->status = 1;
            $m3_result->message = '密码少于六位';
            return $m3_result->toJson();
        }

        if( $password != $confirm ){

            $m3_result->status = 1;
            $m3_result->message = '两次密码不相同';
            return $m3_result->toJson();
        }


        //手机号注册
        if( $phone !='' ){

            if( $phone_code == '' || strlen( $phone_code) != 6 ){
                $m3_result->status = 5;
                $m3_result->message = '手机验证码为6位';
                return $m3_result->toJson();
            }

            $tempPhone = TempPhone::where('phone',$phone)->first();

            if($tempPhone->code = $phone_code){

                //判断验证码有效期时间
                if( time() > strtotime($tempPhone->create_at)){
                    $m3_result->status = 7;
                    $m3_result->message = '手机验证码不正确';
                    return $m3_result->toJson();
                }

                //写入数据库
                $member = new Member();
                $member->phone = $phone;
                $member->password = md5('book' + $password);
                $member->save();

                $m3_result->status = 0;
                $m3_result->message = '注册成功';
                return $m3_result->toJson();
            }

        //邮箱注册
        } else {

            if( $validate_code == '' || strlen( $validate_code) != 4 ){
                $m3_result->status = 6;
                $m3_result->message = '手机验证码为4位';
                return $m3_result->toJson();
            }

            $validate_session = $request->session()->get('validate_code','');
            if( $validate_session  != $validate_code ){
                $m3_result->status = 8;
                $m3_result->message = '验证码不正确';
                return $m3_result->toJson();
            }


            //写入数据库
            $member = new Member();
            $member->email = $email;
            $member->password = md5('book' + $password);
            $member->save();

            $uuid = UUID::create();

            $M3Email = new M3Email();
            $M3Email->to = $email;
            $M3Email->cc = '2275848206@qq.com';
            $M3Email->subject = '星星书店验证邮件';
            $M3Email->content = '请于24小时点击该键接完成验证,http://book.tt/service/doEmail'.'?member_id='. $member->id . '&code=' . $uuid;
            

            $tempEmail = new TempEmail();
            $tempEmail->member_id = $member->id;
            $tempEmail->created_at = date('Y-m-d H:i:s', time() + 24*60*60);
            $tempEmail->code = $uuid;
            $tempEmail->save();


            //发送邮件
            Mail::send('email_register', ['m3_email' => $M3Email], function ($m) use ($M3Email) {
                //$m->from('hello@app.com', 'Your Application');
                $m->to($M3Email->to, '尊敬的用户')
                    ->cc( $M3Email->cc )
                    ->subject( $M3Email->subject );
            });

            $m3_result->status = 0;
            $m3_result->message = '注册成功';
            return $m3_result->toJson();

        }


    }

}
