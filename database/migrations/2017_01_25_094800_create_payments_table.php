<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('payments', function (Blueprint $table) {
      $table->increments('id');
      $table->integer('account_id')->unsigned();
      $table->string('name');
      $table->enum('weekend', ['none','before','after']);
      $table->string('start_date');
      $table->string('end_date');
      $table->string('interval');
      $table->decimal('amount');
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
    Schema::drop('payments');
  }
}
