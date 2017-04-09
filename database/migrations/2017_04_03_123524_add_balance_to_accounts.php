<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBalanceToAccounts extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('accounts', function (Blueprint $table) {
      $table->decimal('balance', 8, 2)->after('type');
      $table->dateTime('balance_date')->after('balance');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('accounts', function (Blueprint $table) {
      $table->dropColumn(['balance',
                          'balance_date']);
    });
  }
}
