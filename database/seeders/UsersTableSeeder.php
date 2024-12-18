<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create(); // Create an instance of Faker for generating random data

        // Seed 50 users (you can adjust this number as needed)
        foreach (range(1, 50) as $index) {
            DB::table('users')->insert([
                'name' => $faker->name,       // Random name
                'email' => $faker->unique()->safeEmail,  // Random email
                'password' => bcrypt('password'), // Default password (you can change this)
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
