<?php

namespace App\Http\Controllers;

class IndexController extends Controller
{


    /**
     * 首页
     * @return $this
     */
    public function index(){

        return view('index');
    }


}
