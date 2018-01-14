<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNutritionToMeals extends Migration
{
  public $nutrition;

  public function __construct()
  {
    $this->nutrition = ['energy',
                        'fat',
                        'saturates',
                        'carb',
                        'sugars',
                        'fibre',
                        'protein',
                        'salt'];
  }

  /**
   * Run the migrations.
   *
   * @return void
   */

  public function up()
  {
    Schema::table('meals', function (Blueprint $table) {
      $last_column = 'portion';
      foreach ($this->nutrition as $column) {
        $table->decimal($column, 5, 2)->after($last_column)->default(0);
        $last_column = $column;
      }
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('meals', function (Blueprint $table) {
      foreach ($this->nutrition as $column) {
        $table->dropColumn($column);
      }
    });
  }
}
