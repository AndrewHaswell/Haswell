<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingredients extends Model
{
  public function meals()
  {
    return $this->belongsToMany('App\Models\Meals','ingredient_meal','ingredient_id', 'meal_id');
  }
}
