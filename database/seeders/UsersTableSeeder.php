<?php

namespace Database\Seeders;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
{
    $faker = Faker::create();

    foreach (range(1, 5) as $index) {
        DB::table('users')->insert([
            'name' => $faker->name,
            'email' => $faker->safeEmail,
            'completed_at' => now(),
            'instructor' => $faker->name,
            'course' => "Javascript Mastery",
            'progress' => 100,
            'completion_status'=>'not completed'
        ]);
    }
}
}
