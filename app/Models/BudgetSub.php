<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetSub extends Model
{
  public function BudgetMain()
  {
    return $this->belongsTo('App\Models\BudgetMain');
  }
}
