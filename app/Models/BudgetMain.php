<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetMain extends Model
{
  public function BudgetSub()
  {
    return $this->hasMany('App\Models\BudgetSub');
  }
}
