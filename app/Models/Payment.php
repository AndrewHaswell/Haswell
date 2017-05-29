<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
  public function accounts()
  {
    return $this->belongsTo('App\Models\Account');
  }

  public function additional()
  {
    return $this->hasMany('App\Models\Additional');
  }
}
