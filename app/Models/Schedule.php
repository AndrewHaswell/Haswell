<?php

namespace App\Models;

use App\Models\Account;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    //
  public function account()
  {
    return $this->belongsTo('Account');
  }
}
