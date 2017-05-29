<?php


namespace App\Http\Controllers\service;

use App\Models\BkwxJsConfig;
use App\Models\M3Result;
use App\Tool\wxpay\WXTool;
use Illuminate\http\Request;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderTemp;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{


    public function orderCommit(Request $request, $product_id)
    {

        $product_ids_arr = ( $product_id != '' ? explode(',', $product_id ) : array() );

        //已登录
        $member = $request->session()->get('member', '');

        $cart_items_arr = array();
        $total_price = 0;
        $name = '';

        $cart_items = Cart::where('member_id','=',$member->id)->whereIn('product_id', $product_ids_arr)->get();

        foreach( $cart_items as $cart_item )
        {
            $cart_item->product = Product::find($cart_item->product_id);
            if( $cart_item->product != null ){
                $total_price += $cart_item->product->price * $cart_item->count;
                $name .= ('<<'.$cart_item->product->name.'>>');
                array_push( $cart_items_arr,  $cart_item);
            }
        }

        $order = new Order;
        $order->name = $name;
        $order->total_price = $total_price;
        $order->member_id = $member->id;
        $order->save();
        $order->order_no = 'E'.time().''.$order->id;
        $order->save();

        foreach( $cart_items_arr as $cart_item){

            $order_item = new OrderTemp();
            $order_item->order_id = $order->id;
            $order_item->product_id = $cart_item->product_id;
            $order_item->count = $cart_item->count;
            $order_item->pdt_snapshot = json_encode( $cart_item->product);
            $order_item->save();

        }


        // JSSDK 相关
        $access_token = WXTool::getAccessToken();
        $jsapi_ticket = WXTool::getJsApiTicket( $access_token );
        $noncestr = WXTool::createNonceStr();
        $timestamp = time();
        $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        //签名
        $signature  = WXTool::signature($jsapi_ticket, $noncestr, $timestamp, $url);
        //返回微信参数
        $bk_wx_js_config = new BkwxJsConfig();
        $bk_wx_js_config->appId = config('wx_config.APPID');
        $bk_wx_js_config->timestamp = $timestamp;
        $bk_wx_js_config->nonceStr = $noncestr;
        $bk_wx_js_config->signature = $signature;


        //return $cart_items_arr;
        return view('order/order_commit')->with('cart_items',  $cart_items_arr)
                                         ->with('total_price', $total_price)
                                         ->with('bk_wx_js_config', $bk_wx_js_config->toJson());

    }



    /**
     * 订单列表
     * @param Request $request
     */
    public function orderList(Request $request)
    {

        $member = $request->session()->get('member','');
        $order = Order::where('member_id','=', $member->id)->get();
        if( $member !='' ){
            $order_items = Order::where('member_id','=', $member->id)->get();
            $order->order_items = $order_items;
            foreach( $order_items as $order_item ){
                $cart_items = Product::find($order_item->product_id);
            }
        }
        return $order;
        return view('order/order_list');
    }


}