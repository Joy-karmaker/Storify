<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Story;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Story::class, function (Faker $faker) {
  $type = $faker->randomElement(['long', 'short']);
  $body = $type=='long'?$faker->paragraph():$faker->text(200);
    return [
        'user_id' => $faker->randomElement(['2', '7']),
        'title' => $faker->unique()->lexify('??????????'),
        'body' => $body,
        'type' => $type,
        'status' => $faker->boolean()
    ];
});
