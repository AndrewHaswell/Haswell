<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{

  public function payments()
  {
    return $this->hasMany('App\Models\Payment');
  }

  public function schedules()
  {
    return $this->hasMany('App\Models\Schedule');
  }

  public function transactions()
  {
    return $this->hasMany('App\Models\Transaction');
  }

}
