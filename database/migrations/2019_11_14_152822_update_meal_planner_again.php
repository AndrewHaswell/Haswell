<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMealPlannerAgain extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('meal_plans')->truncate();
        DB::statement("ALTER TABLE `meal_plans` CHANGE COLUMN `meal` `meal` ENUM('andybreakfast',
                         'breakfast',
                         'lunch',
                         'dinner')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('meal_plans')->truncate();
        DB::statement("ALTER TABLE `meal_plans` CHANGE COLUMN `meal` `meal` ENUM('andybreakfast',
                         'tessabreakfast',
                         'snack1',
                         'andylunch',
                         'tessalunch',
                         'snack2',
                         'dinner')");
    }
}
