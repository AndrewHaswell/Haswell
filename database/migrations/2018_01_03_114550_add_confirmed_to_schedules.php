<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddConfirmedToSchedules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('schedules', function (Blueprint $table) {
          $table->boolean('confirmed', 0)->default(false)->after('transfer');
        });
        Schema::table('payments', function (Blueprint $table) {
          $table->boolean('confirmed', 0)->default(false)->after('transfer_account_id');
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
          $table->dropColumn('confirmed');
        });
        Schema::table('payments', function (Blueprint $table) {
          $table->dropColumn('confirmed');
        });
    }
}
