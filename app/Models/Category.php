<?php
// +----------------------------------------------------------------------
// | Blog [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://lexiang123.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 2275840206@qq.com
// +----------------------------------------------------------------------
// | Time : ${DATA}上午 10:34
// +----------------------------------------------------------------------

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    //绑定数据库表名
    protected $table = 'category';
    //绑定数据库表主健
    protected $primaryKey = 'id';


}