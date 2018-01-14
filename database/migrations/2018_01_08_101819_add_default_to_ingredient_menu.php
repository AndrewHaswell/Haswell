<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDefaultToIngredientMenu extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('ingredient_meal', function (Blueprint $table) {
      $table->dropColumn('unit');
    });
    Schema::table('ingredient_meal', function (Blueprint $table) {
      $table->enum('unit', ['none',
                            'weight',
                            'volume'])->default('none')->after('quantity');
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
      $table->dropColumn('unit');
    });
    Schema::table('ingredient_meal', function (Blueprint $table) {
      $table->enum('unit', ['none',
                            'weight'])->after('quantity');
    });
  }
}
