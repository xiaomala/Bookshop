<?php


namespace App\Http\Controllers\service;

use App\Models\M3Result;
use Illuminate\http\Request;
use App\Models\Cart;
use App\Models\Product;
use App\Http\Controllers\Controller;

class CartController extends Controller
{


    public function toOrderPay(){

        return view('order/order_pay');

    }

}