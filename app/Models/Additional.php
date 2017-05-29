<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Additional extends Model
{
  public function payment()
  {
    return $this->belongsTo('App\Model\Payment');
  }
}
