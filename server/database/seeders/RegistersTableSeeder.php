<?php

namespace Database\Seeders;

use App\Models\Image;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Register;

class RegistersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $register = new Register();
        $register->name = 'Geburtstagsfeier';
        $register->created_at = new \DateTime();
        $register->updated_at = new \DateTime();
        $register->is_public = false;

        $user = User::first();
        $register->user()->associate($user);
        //einfügen - associate
        //rauslöschen - dissociate

        //in die DB speichern
        $register->save();



    }
}
