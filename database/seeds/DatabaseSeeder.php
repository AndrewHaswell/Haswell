<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $user = new App\User;
    $user->name = 'Andrew Haswell';
    $user->email = 'andrew.haswell@imperialoutpost.co.uk';
    $user->password = bcrypt('bob');
    $user->save();

    factory(App\Models\account::class, 12)->create()->each(function ($v) {
      factory(App\Models\balance::class, rand(15, 45))->create(['account_id' => $v->id]);
      factory(App\Models\payment::class, rand(6, 25))->create(['account_id' => $v->id])->each(function ($k) {
        factory(App\Models\balance::class, rand(15, 45))->create(['account_id' => $k->account_id,
                                                                  'payment_id' => $k->id]);
      });
    });
  }
}
