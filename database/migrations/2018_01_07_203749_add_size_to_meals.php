<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSizeToMeals extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('meals', function (Blueprint $table) {
      $table->integer('portion')->after('name')->default(2);
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
      $table->dropColumn('portion');
    });
  }
}
