<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNutritionToIngredients extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */

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
    //$this->nutrition = array_reverse($this->nutrition);
  }

  public function up()
  {
    Schema::table('ingredients', function (Blueprint $table) {
      $last_column = 'category';
      foreach ($this->nutrition as $column) {
        $table->decimal($column, 5, 2)->after($last_column);
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
    Schema::table('ingredients', function (Blueprint $table) {
      foreach ($this->nutrition as $column) {
        $table->dropColumn($column);
      }
    });
  }
}
