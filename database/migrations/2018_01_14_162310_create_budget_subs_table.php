<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBudgetSubsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('budget_subs', function (Blueprint $table) {
      $table->increments('id');
      $table->string('name');
      $table->integer('budget_main_id')->unsigned();
      $table->decimal('balance', 8, 2)->default('0.00');
      $table->timestamps();
      $table->foreign('budget_main_id')->references('id')->on('budget_mains')->onDelete('cascade');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::drop('budget_subs');
  }
}
