<?php

namespace App\Http\Controllers\service;
use App\Models\M3Result;
use App\Models\Member;
use App\Tool\Validate\ValidateCode;
use App\Http\Controllers\Controller;
use App\Tool\SMS\SendTemplateSMS;
use App\Models\TempPhone;
use App\Models\TempEmail;
use App\Http\Requests;
use Illuminate\http\Request;

class ValidateCodeController extends Controller
{


    public function getCreate(Request $request)
    {

        $validateCode = new ValidateCode();

        $request->session()->put('validate_code',$validateCode->getCode());

        return $validateCode->doimg();

    }




    /**
     *  手机注册
     */
    public function doPhone(Request $request)
    {

        $m3_result = new M3Result();

        $phone = $request->input('phone');
        $code = $request->input('code');

        if($phone == ''){
            $m3_result->status = 1;
            $m3_result->message = '手机号不能为空';
            return $m3_result->toJson();
        }

        $sendTemplateSMS = new SendTemplateSMS();
        $charset = '0123456789';
        $code = '';
        $_len = strlen($charset) - 1;
        for ($i=0; $i<6; ++$i){
            $code .= $charset[mt_rand(0, $_len)];
        }

        $m3_result = $sendTemplateSMS->sendTemplateSMS("13480696084", array($code, 6), 1);

        if($m3_result->status == 0) {

            $tempPhone = TempPhone::where('phone', $phone)->first();

            if($tempPhone == null){
                $tempPhone = new TempPhone();
            }

            $tempPhone->phone = $phone;
            $tempPhone->code = $code;
            $tempPhone->created_at = date('Y-m-d H:i:s', time() + 60*60);
            $tempPhone->save();

        }

        return $m3_result->toJson();

    }




    /**
     *  邮箱验证
     */
    public function doEmail( Request $request )
    {
        $member_id = $request->input('member_id','');
        $code = $request->input('code','');

        if( $member_id == '' || $code == '' ){

            return '验证异常';
        }

        $tempEmail = TempEmail::where('member_id',$member_id)->first();

        if( $tempEmail == null ){
            return '验证异常';
        }

        if( $tempEmail->code == $code ){
            if( time() > strtotime( $tempEmail->created_at )){
                return '该键接已失效';
            }

            $member = Member::find( $member_id );
            $member->active = 1;
            $member->save();

            return redirect('/login');

        } else {

            return '该键接已失效';
        }
    }

}
