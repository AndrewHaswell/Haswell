<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMealIngredientTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('ingredient_meal', function (Blueprint $table) {
      $table->increments('id');
      $table->integer('meal_id')->unsigned();
      $table->integer('ingredient_id')->unsigned();

      $table->foreign('meal_id')->references('id')->on('meals')->onDelete('cascade');
      $table->foreign('ingredient_id')->references('id')->on('ingredients')->onDelete('cascade');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::drop('ingredient_meal');
  }
}
