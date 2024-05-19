<?php

namespace Database\Seeders;

use App\Models\Image;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = new User();
        $user->firstName = 'testuser';
        $user->lastName = 'user';
        $user->username = 'TestUser';
        $user->email = 'testuser@gmail.com';
        $user->password = bcrypt('secret');
        $user->profileImage = null;
        $user->save();

        $user1 = new User();
        $user1->firstName = 'Philipp';
        $user1->lastName = 'Ensinger';
        $user1->username = 'Fisch';
        $user1->email = 'p@gmail.com';
        $user1->password = bcrypt('fisch');
        $user1->profileImage = null;
        $user1->save();

        $user2 = new User();
        $user2->firstName = 'Sarah';
        $user2->lastName = 'Kaltseis';
        $user2->username = 'Katze';
        $user2->email = 'test@gmail.com';
        $user2->password = bcrypt('blume');
        $user2->profileImage = null;
        $user2->save();


    }
}
