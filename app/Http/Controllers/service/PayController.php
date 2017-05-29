<?php


namespace App\Http\Controllers\service;

use App\Models\M3Result;
use Illuminate\http\Request;
use App\Http\Controllers\Controller;

class PayController extends Controller
{



    public function index(){

        return view('alipay/index');

    }





    /**
     *  支付
     */
    public function alipay(Request $request)
    {

        require_once(app_path() . "/Tool/alipay/alipay.config.php");
        require_once(app_path() . "/Tool/alipay/lib/alipay_submit.class.php");

        //支付类型
        $payment_type = "1";

        //必填，不能修改
        //服务器异步通知页面路径
        $notify_url = "http://" . $_SERVER['HTTP_HOST'] . '/pay/notify/';
        //需http://格式的完整路径，不能加?id=123这类自定义参数

        //页面跳转同步通知页面路径
        $return_url = "http://" . $_SERVER['HTTP_HOST'] . '/pay/call_back';
        //需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/

        //卖家支付宝帐户
        $seller_email = $_POST['WIDseller_email'];
        //必填

        //商户订单号
        $out_trade_no = $_POST['WIDout_trade_no'];
        //商户网站订单系统中唯一订单号，必填

        //订单名称
        $subject = $_POST['WIDsubject'];
        //必填

        //付款金额
        $total_fee = $_POST['WIDtotal_fee'];
        //必填

        //订单描述

        $body = $_POST['WIDbody'];
        //商品展示地址
        $show_url = $_POST['WIDshow_url'];
        //需以http://开头的完整路径，例如：http://www.xxx.com/myorder.html

        //防钓鱼时间戳
        $anti_phishing_key = "";
        //若要使用请调用类文件submit中的query_timestamp函数

        //客户端的IP地址
        $exter_invoke_ip = "";
        //非局域网的外网IP地址，如：221.0.0.1


        /************************************************************/

        //构造要请求的参数数组，无需改动
        $parameter = array(
            "service" => "create_direct_pay_by_user",
            "partner" => trim($alipay_config['partner']),
            "payment_type"	=> $payment_type,
            "notify_url"	=> $notify_url,
            "return_url"	=> $return_url,
            "seller_email"	=> $seller_email,
            "out_trade_no"	=> $out_trade_no,
            "subject"	=> $subject,
            "total_fee"	=> $total_fee,
            "body"	=> $body,
            "show_url"	=> $show_url,
            "anti_phishing_key"	=> $anti_phishing_key,
            "exter_invoke_ip"	=> $exter_invoke_ip,
            "_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
        );

        //建立请求
        $alipaySubmit = new \AlipaySubmit($alipay_config);
        $html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");

        return $html_text;
    }





    public function notify()
    {

        require_once(app_path() . "/Tool/alipay.config.php");
        require_once(app_path() . "/Tool/lib/alipay_notify.class.php");

        //计算得出通知验证结果
        $alipayNotify = new \AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyNotify();

        if($verify_result){

            $out_trade_no = $_POST['out_trade_no'];

            //支付宝交易号

            $trade_no = $_POST['trade_no'];

            //交易状态
            $trade_status = $_POST['trade_status'];


            if($_POST['trade_status'] == 'TRADE_FINISHED'){


            }else if ($_POST['trade_status'] == 'TRADE_SUCCESS'){


            }

            echo "success";		//请不要修改或删除

        } else {

            //验证失败
            echo "fail";

        }

    }




    /**
     * 返回路径
     */
    public function call_back()
    {

        require_once(app_path() . "/Tool/alipay.config.php");
        require_once(app_path() . "/Tool/lib/alipay_notify.class.php");

        //计算得出通知验证结果
        $alipayNotify = new \AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyReturn();
        if($verify_result) {
            //验证成功
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //请在这里加上商户的业务逻辑程序代码

            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
            //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表

            //商户订单号
            $out_trade_no = $_GET['out_trade_no'];

            //支付宝交易号
            $trade_no = $_GET['trade_no'];

            //交易状态
            $trade_status = $_GET['trade_status'];


            if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序
            } else {
                echo "trade_status=".$_GET['trade_status'];
            }

            echo "验证成功<br />";

            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        } else {
            //验证失败
            //如要调试，请看alipay_notify.php页面的verifyReturn函数
            echo "验证失败";
        }


    }






    public function wxPay(Request $request)
    {
        $openid = $request->session()->get('openid', '');
        if($openid == '') {
            $m3_result = new M3Result;
            $m3_result->status = 1;
            $m3_result->message = 'Session已过期, 请重新提交订单';
            return $m3_result;
        }
        return WXTool::wxPayData($request->input('name'), $request->input('order_no'), 1, $openid);
    }




    //微信支付回调开始
    public function wxNotify()
    {
        Log::info('微信支付回调开始');
        $return_data = file_get_contents("php://input");

        Log::info('return_data: '.$return_data);
        libxml_disable_entity_loader(true);

        $data = simplexml_load_string($return_data, 'SimpleXMLElement', LIBXML_NOCDATA);
        Log::info('return_code: '.$data->return_code);

        if($data->return_code == 'SUCCESS') {
            $order = Order::where('order_no', $data->out_trade_no)->first();
            $order->status = 2;
            $order->save();
            return "<xml>
                <return_code><![CDATA[SUCCESS]]></return_code>
                <return_msg><![CDATA[OK]]></return_msg>
              </xml>";
        }
        return "<xml>
              <return_code><![CDATA[FAIL]]></return_code>
              <return_msg><![CDATA[FAIL]]></return_msg>
            </xml>";
    }



}