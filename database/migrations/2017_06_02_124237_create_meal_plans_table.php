<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMealPlansTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('meal_plans', function (Blueprint $table) {
      $table->increments('id');
      $table->enum('day', ['sunday',
                           'monday',
                           'tuesday',
                           'wednesday',
                           'thursday',
                           'friday',
                           'saturday']);
      $table->integer('meal_id')->unsigned();
      $table->enum('meal', ['breakfast',
                            'lunch',
                            'dinner',
                            'snack1',
                            'snack2']);
      $table->timestamps();
      $table->unique(['day',
                      'meal']);
      $table->foreign('meal_id')->references('id')->on('meals')->onDelete('cascade');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::drop('meal_plans');
  }
}
