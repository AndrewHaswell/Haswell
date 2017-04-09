<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\Models\payment::class, function (Faker\Generator $faker) {

  $payment_types = ['direct_debit',
                    'expense'];
  $type = $payment_types[mt_rand(0, count($payment_types) - 1)];

  return ['name'       => $faker->name,
          'type'       => $type,
          'amount'     => (rand(200, 10000) / 100),
          'fixed_date' => rand(1, 28),];
});
