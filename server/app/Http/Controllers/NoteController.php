<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Note;
use App\Models\Register;
use App\Models\Tag;
use App\Models\Todo;
use App\Models\User;
use DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NoteController extends Controller
{
    public function index():JsonResponse{
        //load all Registers with all relations wit eager loading
        $notes = Note::with(['register', 'user', 'todos', 'tags', 'images'])->get();
        return response()->json($notes, 200);
    }

    public function findByID(string $id):JsonResponse{
        $note = Note::where('id', $id)->with(['register', 'user', 'todos', 'tags', 'images'])->first();
        return $note!=null ? response()->json($note, 200) : response()->json(null, 200);
    }

    public function checkID(string $id):JsonResponse{
        $note = Note::where('id', $id)->first();
        return $note!=null ? response()->json(true, 200) : response()->json(false, 200);
    }

    public function save(Request $request):JsonResponse{
        $request = $this->parseRequest($request);
        //Starten eine DB Transaktion
        DB::beginTransaction();
        try{
            $note = Note::create($request->all());
            if(isset($request['todos']) && is_array($request['todos'])) {
                foreach ($request['todos'] as $todo) {
                    $todo = Todo::firstOrNew(['title' => $todo['title']]);
                    $note->todos()->save($todo);
                }
            }
            if(isset($request['user']) && is_array($request['user'])) {
                foreach ($request['user'] as $user) {
                    $user = User::firstOrNew(['firstName' => $user['firstName'], 'lastName' => $user['lastName']]);
                    $note->users()->save($user);
                }
            }

            //N:M Beziehung
            $note->tags()->sync($request['tags']);
            $note->save();

            if(isset($request['images']) && is_array($request['images'])){
                foreach ($request['images'] as $img){
                    $image = Image::firstOrNew(['url'=>$img['url'], 'caption'=>$img['caption']]);
                    $note->images()->save($image);
                }
            }
            DB::commit();
            return response()->json($note ,201);

        }
        catch(\Exception $e){
            DB::rollBack();
            return response()->json("saving note failed ". $e->getMessage(), 420);
        }
    }

    public function update(Request $request, string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $note = Note::with( 'tags', 'todos', 'images')->
            where('id', $id)->first();


            if ($note != null) {
                $note->update($request->all());

                if(isset($request['images']) && is_array($request['images'])){
                    foreach ($request['images'] as $img){
                        $image = Image::firstOrNew(['url'=>$img['url'], 'caption'=>$img['caption']]);
                        $note->images()->save($image);
                    }
                }

                //update tags
                $tag_ids = [];
                if (isset($request['tags']) && is_array($request['tags'])) {
                    foreach ($request['tags'] as $t) {
                        array_push($tag_ids, $t);
                    }
                }
                $note->tags()->sync($tag_ids);
                $note->save();

                // Update todos relation if needed
                if (isset($request['todos'])) {
                    $todoIds = array_column($request['todos'], 'id');
                    $note->todos()->sync($todoIds);
                }
                $note->save();

            }
            DB::commit();

            $note_new = Note::with('tags', 'todos', 'images')->
            where('id', $id)->first();
            return response()->json($note_new, 201);


        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json("updating note failed: " . $e->getMessage(), 420);

        }
    }

    public function delete(string $id):JsonResponse{
        $note= Note::where('id', $id)->first();
        if($note!=null){
            $note->delete();
            return response()->json('note ('. $id . ') successfully deleted', 200);
        } else{
            return response()->json("could not delete note - it does not exist ", 422);
        }
    }

    private function parseRequest(Request $request):Request{
        //get date and covert it - it is in ISO 8601, "2024-04-21T16:29:00.000Z"
        $date = new DateTime($request->created_at);
        $request['created_at'] = $date->format('Y-m-d H:i:s');
        return $request;
    }
}
