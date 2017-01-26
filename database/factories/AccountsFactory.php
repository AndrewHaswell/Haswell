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

$factory->define(App\Models\account::class, function (Faker\Generator $faker) {

  //$faker->addProvider(new Faker\Provider\en_US\Address($faker));

  $account_types = ['current',
                    'credit',
                    'loan'];
  $type = $account_types[mt_rand(0, count($account_types) - 1)];

  return ['name' => $faker->domainWord,
          'type' => $type];
});
