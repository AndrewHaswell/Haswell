<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('payment', function (Blueprint $table) {
      $table->increments('id');
      $table->integer('account_id')->unsigned();
      $table->string('name');
      $table->timestamps();
      $table->foreign('account_id')->references('id')->on('account')->onDelete('cascade');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::drop('payment');
  }
}
