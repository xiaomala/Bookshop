<?php

namespace App\Models;

class M3Result {

  //状态
  public $status;
  //信息返回
  public $message;

  public function toJson()
  {
    return json_encode($this, JSON_UNESCAPED_UNICODE);
  }

}
