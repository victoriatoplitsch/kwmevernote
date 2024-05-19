<?php

namespace Database\Seeders;

use App\Models\Image;
use App\Models\Note;
use App\Models\Register;
use App\Models\Todo;
use App\Models\User;
use DateTime;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TodosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $todo = new Todo();
        $todo->title = "Einladungen schreiben";
        $todo->description = "An mind. 10 Personen - Familie nicht vergessen ";
        $todo->due_date = new DateTime();
        $todo->created_at = new \DateTime();
        $todo->updated_at = new \DateTime();


        $note = Note::first();
        $todo->note()->associate($note);

        $user = User::first();
        $todo->user()->associate($user);

        $todo->save();

        $image2 = new Image();
        $image2->url ="https://m.media-amazon.com/images/I/714FBX7ACbL.__AC_SX300_SY300_QL70_ML2_.jpg";
        $image2->caption = "Einladung 1";

        $image1 = new Image();
        $image1->url ="https://m.media-amazon.com/images/I/81Iy9v-K9VL.__AC_SY300_SX300_QL70_ML2_.jpg";
        $image1->caption = "Einladung 2";


        $todo->images()->saveMany([$image1, $image2]);

        $todo->save();


    }
}
