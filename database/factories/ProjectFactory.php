<?php

use Faker\Generator as Faker;

$factory->define(App\Project::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence,
        'description' => $faker->text,
        'owner_id' => function () {
            return factory(App\User::class)->create()->id;
        }
    ];
});

$factory->define(App\Task::class, function (Faker $faker) {
    return [
        'body' => $faker->sentence,
        'project_id' => function () {
            return factory(App\Project::class)->create()->id;
        }
    ];
});
