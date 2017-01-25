<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('account', function (Blueprint $table) {
      $table->increments('id');
      $table->string('account_name');
      $table->enum('type', ['current_account',
                            'credit_card',
                            'loan']);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::drop('account');
  }
}
