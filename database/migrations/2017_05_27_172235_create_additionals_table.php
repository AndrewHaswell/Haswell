<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdditionalsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('additionals', function (Blueprint $table) {
      $table->increments('id');
      $table->integer('account_id')->unsigned();
      $table->integer('payment_id')->unsigned();
      $table->string('name');
      $table->integer('weekday');
      $table->string('start_date');
      $table->string('end_date');
      $table->decimal('amount');
      $table->enum('type', ['credit',
                            'debit',
                            'transfer']);
      $table->integer('transfer_account_id')->unsigned();
      $table->timestamps();
    });

    Schema::table('additionals', function ($table) {
      $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
      $table->foreign('payment_id')->references('id')->on('payments')->onDelete('cascade');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::drop('additionals');
  }
}
