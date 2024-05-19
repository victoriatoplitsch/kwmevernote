<?php

namespace Database\Seeders;

use App\Models\Image;
use App\Models\Note;
use App\Models\Register;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $note = new Note();
        $note->title = 'Einladungen';
        $note->description = "Wer soll alle kommen?";
        $note->created_at = new \DateTime();
        $note->updated_at = new \DateTime();

        $register = Register::first();
        $note->register()->associate($register);

        $tags = Tag::all()->pluck("id");
        $note->tags()->sync($tags);

        $note->save();

        $image2 = new Image();
        $image2->url ="https://m.media-amazon.com/images/I/71kB3RdFYTL._SX342_.jpg";
        $image2->caption = "Cover";

        $image1 = new Image();
        $image1->url ="https://m.media-amazon.com/images/I/41I7HSpy4dL._SY445_SX342_.jpg";
        $image1->caption = "Cover";


        $note->images()->saveMany([$image1, $image2]);

        $note->save();
    }
}
