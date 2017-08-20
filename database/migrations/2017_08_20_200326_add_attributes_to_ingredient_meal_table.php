<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAttributesToIngredientMealTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('ingredient_meal', function (Blueprint $table) {
      $table->integer('quantity');
      $table->enum('unit', ['none',
                            'weight',
                            'volume']);
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('ingredient_meal', function (Blueprint $table) {
      $table->dropColumn(['quantity',
                          'unit']);
    });
  }
}
