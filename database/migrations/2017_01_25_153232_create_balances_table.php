<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBalancesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('balances', function (Blueprint $table) {
      $table->increments('id');
      $table->integer('account_id')->unsigned();
      $table->integer('payment_id')->unsigned();
      $table->decimal('balance');
      $table->timestamps();
      $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::drop('balances');
  }
}
