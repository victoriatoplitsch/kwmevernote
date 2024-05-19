<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        //load all Registers with all relations wit eager loading
        $users = User::with([])->get();
        return response()->json($users, 200);
    }

    public function findByID(string $id): JsonResponse
    {
        $user = User::where('id', $id)->with([])->first();
        return $user != null ? response()->json($user, 200) : response()->json(null, 200);
    }
}



