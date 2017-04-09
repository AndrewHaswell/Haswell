<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDatesToPaymentsAndSchedules extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('schedules', function (Blueprint $table) {
      $table->datetime('payment_date')->after('name');
    });
    Schema::table('transactions', function (Blueprint $table) {
      $table->datetime('payment_date')->after('name');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('schedules', function (Blueprint $table) {
      $table->dropColumn('payment_date');
    });
    Schema::table('transactions', function (Blueprint $table) {
      $table->dropColumn('payment_date');
    });
  }
}
