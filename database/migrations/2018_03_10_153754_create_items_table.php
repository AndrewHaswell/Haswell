<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('items', function (Blueprint $table) {
      $table->increments('id');
      $table->string('name');
      $table->string('shop');
      $table->decimal('price');
      $table->string('category');
      $table->integer('qty')->default(1);
      $table->boolean('recurring', 0)->default(false);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::drop('items');
  }
}
