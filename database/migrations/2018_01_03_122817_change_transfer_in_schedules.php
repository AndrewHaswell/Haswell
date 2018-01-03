<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTransferInSchedules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      DB::statement("ALTER TABLE `schedules` CHANGE `transfer` `transfer` INT NOT NULL DEFAULT '0'");
    }

  /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      DB::statement("ALTER TABLE `schedules` CHANGE `transfer` `transfer` TINYINT(1)  NOT NULL  DEFAULT '0'");
    }
}
