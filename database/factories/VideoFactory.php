<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Video;
use Faker\Generator as Faker;

$factory->define(Video::class, function (Faker $faker) {
    $rating = Video::RATING_LIST[array_rand(Video::RATING_LIST)];
    return [
        'title' => $faker->sentence(3),
        'description' => $faker->sentence(10),
        'year_launched' => rand(1895, 2020),
        'opened' => rand(0, 1),
        'rating' => $rating,
        'duration' => rand(1, 30),
        /*'thumb_file' => null,
        'banner_file' => null,
        'trailer_file' => null,
        'video_file' => null,
        'published' => rand(0, 1),*/
    ];
});
