<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{

    public function run(): void
    {

        DB::table('users')->insert([
            'username' => "user1",
            'password' => Hash::make('123456'),
        ]);
        DB::table('users')->insert([
            'username' => "user2",
            'password' => Hash::make('123456'),
        ]);

        DB::table('sponsors')->insert([
            'name' => "sponsor1",
            'number' => "123456",
            'user_id' => "1"
        ]);
        DB::table('sponsors')->insert([
            'name' => "sponsor2",
            'number' => "123456",
            'user_id' => "2"
        ]);

        DB::table('cars')->insert([
            'name' => "car1",
            'plate' => "12345-O",
            'model' => "2000",
            'user_id' => "1",
            'image_path' => "cars_images/car_1709497411.jpg",
        ]);
        DB::table('cars')->insert([
            'name' => "car2",
            'plate' => "12345-O",
            'model' => "2020",
            'user_id' => "2",
            'image_path' => "cars_images/car_1709497434.jpg",
        ]);

        DB::table('clients')->insert([
            'user_id' => "1",
            'sponsor_id' => "1",
            'name' => "client1",
            'number' => "81 345 756",
            'address' => "kfaromen",
            'front_image_path' => "id_images/front_1709497669.jpg",
            'back_image_path' => "id_images/back_1709497669.jpg",
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);
        DB::table('clients')->insert([
            'user_id' => "1",
            'sponsor_id' => null,
            'name' => "client2",
            'number' => "81 345 756",
            'address' => "kfaromen",
            'front_image_path' => "id_images/front_1709497669.jpg",
            'back_image_path' => "id_images/back_1709497669.jpg",
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);
        DB::table('clients')->insert([
            'user_id' => "2",
            'sponsor_id' => "2",
            'name' => "client3",
            'number' => "81 345 756",
            'address' => "kfaromen",
            'front_image_path' => "id_images/front_1709497669.jpg",
            'back_image_path' => "id_images/back_1709497669.jpg",
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);
        DB::table('clients')->insert([
            'user_id' => "2",
            'sponsor_id' => "2",
            'name' => "client4",
            'number' => "81 345 756",
            'address' => "kfaromen",
            'front_image_path' => "id_images/front_1709497669.jpg",
            'back_image_path' => "id_images/back_1709497669.jpg",
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);
    }
}
