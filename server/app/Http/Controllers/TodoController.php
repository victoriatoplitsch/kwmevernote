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

class TodoController extends Controller
{
    public function index():JsonResponse{
        //load all Registers with all relations wit eager loading
        $todos = Todo::with(['user', 'note', 'tags', 'images'])->get();
        return response()->json($todos, 200);
    }

    public function findByID(string $id):JsonResponse{
        $todo = Todo::where('id', $id)->with(['user', 'note', 'tags', 'images'])->first();
        return $todo!=null ? response()->json($todo, 200) : response()->json(null, 200);
    }

    public function checkID(string $id):JsonResponse{
        $todo = Todo::where('id', $id)->first();
        return $todo!=null ? response()->json(true, 200) : response()->json(false, 200);
    }

    public function save(Request $request):JsonResponse{
        $request = $this->parseRequest($request);
        //Starten eine DB Transaktion
        DB::beginTransaction();
        try{
            $todo = Todo::create($request->all());

            if(isset($request['note']) && is_array($request['note'])) {
                foreach ($request['note'] as $n) {
                    $note = Note::firstOrNew(['title' => $n['title']]);
                    $todo->notes()->save($note);
                }
            }
            if (isset($request['images']) && is_array($request['images'])){
                foreach ($request['images'] as $img){
                    $image = Image::firstOrNew(['url'=>$img['url'],'caption'=>$img['caption']]);
                    $todo->images()->save($image);
                }
            }
            if(isset($request['user']) && is_array($request['user'])) {
                foreach ($request['user'] as $user) {
                    $user = User::firstOrNew(['firstName' => $user['firstName'], 'lastName' => $user['lasttName']]);
                    $todo->users()->save($user);
                }
            }

            $todo->tags()->sync($request['tags']);
            $todo->save();

            DB::commit();
            return response()->json($todo, 201);
        }
        catch(\Exception $e){
            DB::rollBack();
            return response()->json("saving todo failed ". $e->getMessage(), 420);
        }
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $request = $this->parseRequest($request);
        DB::beginTransaction();
        try {
            $todo = Todo::with( 'user', 'tags', 'images')->
            where('id', $id)->first();


            if ($todo != null) {
                $todo->update($request->all());

                if(isset($request['images']) && is_array($request['images'])){
                    foreach ($request['images'] as $img){
                        $image = Image::firstOrNew(['url'=>$img['url'], 'caption'=>$img['caption']]);
                        $todo->images()->save($image);
                    }
                }

                //update tags
                $tag_ids = [];
                if (isset($request['tags']) && is_array($request['tags'])) {
                    foreach ($request['tags'] as $t) {
                        array_push($tag_ids, $t);
                    }
                }
                $todo->tags()->sync($tag_ids);
                $todo->save();


            }
            DB::commit();

            $todo_new = Todo::with('user', 'tags', 'images')->
            where('id', $id)->first();
            return response()->json($todo_new, 201);


        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json("updating todo failed: " . $e->getMessage(), 420);

        }
    }

    public function delete(string $id):JsonResponse{
        $todo = Todo::where('id', $id)->first();
        if($todo!=null){
            $todo->delete();
            return response()->json('todo ('. $id . ') successfully deleted', 200);
        } else{
            return response()->json("could not delete todo - it does not exist ", 422);
        }
    }

    private function parseRequest(Request $request):Request{
        $date = new DateTime($request->due_date);
        $request['due_date'] = $date->format('Y-m-d H:i:s');
        return $request;
    }
}
