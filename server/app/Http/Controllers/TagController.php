<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Tag;
use App\Models\Todo;
use App\Models\User;
use DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TagController extends Controller
{

    public function index(): JsonResponse
    {
        //load all Registers with all relations wit eager loading
        $tags = Tag::with(['notes', 'todos'])->get();
        return response()->json($tags, 200);
    }

    public function findByID(string $id): JsonResponse
    {
        $tag = Tag::where('id', $id)->with(['notes', 'todos'])->first();
        return $tag != null ? response()->json($tag, 200) : response()->json(null, 200);
    }

    public function checkID(string $id): JsonResponse
    {
        $tag = Tag::where('id', $id)->first();
        return $tag != null ? response()->json(true, 200) : response()->json(false, 200);
    }

    public function findBySearchTerm(string $searchTerm):JsonResponse{
        $tags = Tag::with(['notes', 'todos'])
            ->where('name',  'LIKE','%'.$searchTerm.'%')
            /*Beziehung zu Notiz*/
            ->orWhereHas('notes', function($query) use ($searchTerm){
                $query->where('title',  'LIKE','%'.$searchTerm.'%')
                    ->orWhere('description',  'LIKE','%'.$searchTerm.'%');
            })->get();
        return response()->json($tags, 200);
    }

    public function save(Request $request): JsonResponse
    {
        $request = $this->parseRequest($request);
        //Starten eine DB Transaktion
        DB::beginTransaction();
        try {
            $tag = Tag::create($request->all());
            if (isset($request['note']) && is_array($request['note'])) {
                foreach ($request['note'] as $note) {
                    $note = Note::firstOrNew(['title' => $note['title']]);
                    $note->notes()->save($tag);
                }
            }
            if (isset($request['todo']) && is_array($request['todo'])) {
                foreach ($request['todo'] as $todo) {
                    $todo = Todo::firstOrNew(['title' => $todo['title']]);
                    $tag->todos()->save($todo);
                }
            }
            DB::commit();
            return response()->json($tag, 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json("saving tag failed " . $e->getMessage(), 420);
        }
    }

    public function update(Request $request, string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $tag = Tag::with('notes', 'todos')->
            where('id', $id)->first();


            if ($tag != null) {
                $tag->update($request->all());


                // Update notes relation if needed
                if (isset($request['notes'])) {
                    $noteIds = array_column($request['notes'], 'id');
                    $tag->notes()->sync($noteIds);
                }

                // Update todos relation if needed - mitHilfe von ChatGPT
                if (isset($request['todos'])) {
                    $todoIds = array_column($request['todos'], 'id');
                    $tag->todos()->sync($todoIds);
                }
                $tag->save();

            }
            DB::commit();

            $tag_new = Tag::with('notes', 'todos')->
            where('id', $id)->first();
            return response()->json($tag_new, 201);


        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json("updating tag failed: " . $e->getMessage(), 420);

        }
    }

    public function delete(string $id): JsonResponse
    {
        $tag = Tag::where('id', $id)->first();
        if ($tag != null) {
            $tag->delete();
            return response()->json('tag (' . $id . ') successfully deleted', 200);
        } else {
            return response()->json("could not delete tag - it does not exist ", 422);
        }
    }

    private function parseRequest(Request $request): Request
    {
        //get date and covert it - it is in ISO 8601, "2024-04-21T16:29:00.000Z"
        $date = new DateTime($request->created_at);
        $request['created_at'] = $date->format('Y-m-d H:i:s');
        return $request;
    }

}
