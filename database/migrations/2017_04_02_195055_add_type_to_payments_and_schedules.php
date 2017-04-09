<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeToPaymentsAndSchedules extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('schedules', function (Blueprint $table) {
      $table->enum('type', ['credit',
                            'debit'])->after('payment_date');
    });
    Schema::table('transactions', function (Blueprint $table) {
      $table->enum('type', ['credit',
                            'debit'])->after('payment_date');
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
      $table->dropColumn('type');
    });
    Schema::table('transactions', function (Blueprint $table) {
      $table->dropColumn('type');
    });
  }
}
