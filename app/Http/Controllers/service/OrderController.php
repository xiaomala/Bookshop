<?php


namespace App\Http\Controllers\service;

use App\Models\M3Result;
use Illuminate\http\Request;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderTemp;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{


    public function orderCommit(Request $request,  $product_id)
    {

        $product_ids_arr = ( $product_id != '' ? explode(',', $product_id ) : array() );

        //已登录
        $member = $request->session()->get('memeber', '');


        $cart_items_arr = array();
        $total_price = 0;

        if($member !='' ){

            $cart_items = Cart::where('member_id','=',$member->id)->whereIn('product_id', $product_ids_arr)->get();

            foreach( $cart_items as $cart_item )
            {
                $cart_item->product = Product::find($cart_item->product_id);
                if( $cart_item->product != null ){
                    $total_price += $cart_item->product->price * $cart_item->count;
                    array_push( $cart_items_arr,  $cart_item);
                }
            }

        }

        //return $cart_items_arr;
        return view('order/order_commit')->with('cart_items',  $cart_items_arr)
                                         ->with('total_price', $total_price);

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