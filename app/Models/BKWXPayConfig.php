<?php
// +----------------------------------------------------------------------
// | Blog [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://lexiang123.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 2275840206@qq.com
// +----------------------------------------------------------------------
// | Time : ${DATA}下午 9:13
// +----------------------------------------------------------------------

namespace App\Models;

class BKWXPayConfig extends M3Result {

    public $timestamp;
    public $nonceStr;
    public $package;
    public $signType;
    public $paySign;

}
