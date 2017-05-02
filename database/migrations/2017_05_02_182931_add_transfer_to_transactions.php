<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTransferToTransactions extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('transactions', function (Blueprint $table) {
      $table->boolean('transfer', 0)->default(false)->after('confirmed');
    });
    Schema::table('schedules', function (Blueprint $table) {
      $table->boolean('transfer', 0)->default(false)->after('account_id');
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
      $table->dropColumn('transfer');
    });
    Schema::table('schedules', function (Blueprint $table) {
      $table->dropColumn('transfer');
    });
  }
}
