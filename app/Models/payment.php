<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class payment extends Model
{
    //

  public function accounts()
  {
    return $this->belongsTo('App\Model\account');
  }
}
