<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MealPlan extends Model
{
  protected $fillable = ['day','meal_id','meal'];
}
