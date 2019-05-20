<?php

use Faker\Generator as Faker;

$factory->define(App\Project::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence,
        'description' => $faker->text,
        'owner_id' => factory(App\User::class),
        'notes' => 'foobar notes'
    ];
});

$factory->define(App\Task::class, function (Faker $faker) {
    return [
        'body' => $faker->sentence,
        'project_id' =>  factory(App\Project::class)
    ];
});
