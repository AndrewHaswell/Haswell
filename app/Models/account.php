<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class account extends Model
{
  public function payments()
  {
    return $this->hasMany('App\Models\payment');
  }
}
