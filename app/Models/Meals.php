<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meals extends Model
{
  public function ingredients()
  {
    return $this->belongsToMany('App\Models\Ingredients','ingredient_meal', 'meal_id','ingredient_id')->withPivot('quantity','unit');
  }
}
