<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Register;
use App\Models\User;
use DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;


class RegisterController extends Controller
{
    public function index():JsonResponse{
        //load all Registers with all relations wit eager loading
        $registers = Register::with(['user', 'notes'])->get();
        return response()->json($registers, 200);
    }

    public function findByID(string $id):JsonResponse{
        $register = Register::where('id', $id)->with(['user', 'notes'])->first();
        return $register!=null ? response()->json($register, 200) : response()->json(null, 200);
    }

    public function checkID(string $id):JsonResponse{
        $register = Register::where('id', $id)->first();
        return $register!=null ? response()->json(true, 200) : response()->json(false, 200);
    }

    public function save(Request $request):JsonResponse{
        $request = $this->parseRequest($request);
        //Starten eine DB Transaktion
        DB::beginTransaction();
        try{
            $register = Register::create($request->all());
            if(isset($request['note']) && is_array($request['note'])) {
                foreach ($request['note'] as $note) {
                    $note = Note::firstOrNew(['title' => $note['title']]);
                    $register->notes()->save($note);
                }
            }
            if(isset($request['user']) && is_array($request['user'])){
                foreach ($request['user'] as $user){
                    $user = User::firstOrNew(['firstName'=>$user['firstName'], 'lastName'=>$user['lastName']]);
                    $register->users()->save($user);
                }
            }
            DB::commit();
            return response()->json($register, 201);

        }
        catch(\Exception $e){
            DB::rollBack();
            return response()->json("saving list failed ". $e->getMessage(), 420);
        }
    }

    public function update(Request $request, string $id):JsonResponse{
        //Starten eine DB Transaktion
        DB::beginTransaction();
        try{
            $register = Register::with('user', 'notes')->
            where('id', $id)->first();

            if($register!=null){
                $request = $this->parseRequest($request);
                $register->update($request->all());

                // Update notes relation if needed
                if (isset($request['notes'])) {
                    $noteIds = array_column($request['notes'], 'id');
                    $register->notes()->sync($noteIds);
                }

                $id = [];
                if(isset($request['user']) && is_array($request['user'])){
                    foreach ($request['user'] as $user) {
                        array_push($ids, $user['id']);
                    }
                }
                $register->save();
            }
            DB::commit();
            $register1 = Register::with('user', 'note')->
            where('id', $id)->first();
            return response()->json($register1, 201);

        }
        catch(\Exception $e){
            DB::rollBack();
            return response()->json("updating list failed ". $e->getMessage(), 420);
        }
    }

    public function delete(string $id):JsonResponse{
        $register = Register::where('id', $id)->first();
        if($register!=null){
            $register->delete();
            return response()->json('register ('. $id . ') successfully deleted', 200);
        } else{
            return response()->json("could not delete book - it does not exist ", 422);
        }
    }

    private function parseRequest(Request $request):Request{
        //get date and covert it - it is in ISO 8601, "2024-04-21T16:29:00.000Z"
        $date = new DateTime($request->created_at);
        $request['created_at'] = $date->format('Y-m-d H:i:s');
        return $request;
    }
}
