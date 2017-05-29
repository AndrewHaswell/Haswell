<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeAccountOptions extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    DB::statement("ALTER TABLE `accounts` CHANGE COLUMN `type` `type` ENUM('current','credit','saving','loan','cash')");
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    DB::statement("ALTER TABLE `accounts` CHANGE COLUMN `type` `type` ENUM('current','credit','loan','cash')");
  }
}
