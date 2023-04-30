<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PublicController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

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

Route::group(['prefix' => 'auth'], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'registration']);
});
Route::group(['prefix' => 'public'], function () {
    Route::get('/posts', [PublicController::class, 'index']);
});


Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('post/get', [PostController::class, 'index']);
    Route::post('post/store', [PostController::class, 'store']);
    Route::post('post/delete', [PostController::class, 'delete']);
    Route::post('post/update', [PostController::class, 'update']);
});
