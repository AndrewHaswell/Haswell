<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHiddenToAccounts extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('accounts', function (Blueprint $table) {
      $table->boolean('hidden', 0)->default(false)->after('balance');
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
      $table->dropColumn('hidden');
    });
  }
}
