<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class payment extends Model
{


  public function account()
  {
    return $this->belongsTo('App\Models\account');
  }

}
