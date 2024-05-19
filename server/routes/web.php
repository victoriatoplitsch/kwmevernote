<?php

use App\Models\Note;
use App\Models\List;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [\App\Http\Controllers\RegisterController::class, "index"]);
Route::get('/registers', [\App\Http\Controllers\RegisterController::class, "index"]);

Route::get('/registers/{id}', function ($id) {
    $registers = \App\Models\Register::find($id);
    return view('registers.show', compact('registers'));
});

