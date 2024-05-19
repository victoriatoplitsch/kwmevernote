<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Listen
Route::get('registers', [RegisterController::class, 'index']);
Route::get('registers/{id}', [RegisterController::class, 'findByID']);
Route::get('registers/checkid/{id}', [RegisterController::class, 'checkID']);


//Notizen
Route::get('notes', [NoteController::class, 'index']);
Route::get('notes/{id}', [NoteController::class, 'findByID']);
Route::get('notes/checkid/{id}', [NoteController::class, 'checkID']);


//Todos
Route::get('todos', [TodoController::class, 'index']);
Route::get('todos/{id}', [TodoController::class, 'findByID']);
Route::get('todos/checkid/{id}', [TodoController::class, 'checkID']);


//Tags
Route::get('tags', [TagController::class, 'index']);
Route::get('tags/{id}', [TagController::class, 'findByID']);
Route::get('tags/checkid/{id}', [TagController::class, 'checkID']);
Route::get('tags/search/{searchTerm}', [TagController::class, 'findBySearchTerm']);


//Users
Route::get('users', [UserController::class, 'index']);
Route::get('users/{id}', [UserController::class, 'findByID']);


//Login
Route::post('auth/login', [AuthController::class, 'login']);

Route::group(['middleware'=>['api','auth.jwt']], function (){

    //Listen - Admin
    Route::post('registers', [RegisterController::class, 'save']);
    Route::put('registers/{id}', [RegisterController::class, 'update']);
    Route::delete('registers/{id}', [RegisterController::class, 'delete']);

    //Notizen -Admin
    Route::post('notes', [NoteController::class, 'save']);
    Route::put('notes/{id}', [NoteController::class, 'update']);
    Route::delete('notes/{id}', [NoteController::class, 'delete']);

    //Todos -Admin
    Route::post('todos', [TodoController::class, 'save']);
    Route::put('todos/{id}', [TodoController::class, 'update']);
    Route::delete('todos/{id}', [TodoController::class, 'delete']);

    //Tags -Admin
    Route::post('tags', [TagController::class, 'save']);
    Route::put('tags/{id}', [TagController::class, 'update']);
    Route::delete('tags/{id}', [TagController::class, 'delete']);

    //Logout
    Route::post('auth/logout', [AuthController::class, 'logout']);
});
