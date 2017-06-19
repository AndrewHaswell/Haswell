<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScheduleUpdatesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::dropIfExists('schedule_updates');

    Schema::create('schedule_updates', function (Blueprint $table) {
      $table->increments('id');
      $table->string('name');
      $table->datetime('payment_date');
      $table->enum('type', ['credit',
                            'debit']);
      $table->integer('account_id');
      $table->tinyInteger('transfer');
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
    Schema::drop('schedule_updates');
  }
}
