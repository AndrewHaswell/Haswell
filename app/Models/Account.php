<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
  //

  public function payments()
  {
    return $this->hasMany('App\Models\Payment');
  }

  public function balances()
  {
    return $this->hasMany('App\Models\Balance');
  }
}
