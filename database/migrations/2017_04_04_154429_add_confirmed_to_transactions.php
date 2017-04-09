<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddConfirmedToTransactions extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('transactions', function (Blueprint $table) {
      $table->boolean('confirmed', 0)->default(false)->after('account_id');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('transactions', function (Blueprint $table) {
      $table->dropColumn('confirmed');
    });
  }
}
