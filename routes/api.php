<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\GoogleController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);
Route::post('/auth/login', [GoogleController::class, 'login']);
Route::middleware('auth:sanctum')->get('/auth/user', [GoogleController::class, 'user']);
Route::middleware('auth:sanctum')->post('/auth/logout', [GoogleController::class, 'logout']);

//List users
Route::get('/users', [UserController::class, 'index']);
//List single user
Route::get('/user/{id}', [UserController::class, 'show']);
//Create new user
Route::post('/user', [UserController::class, 'store']);
//Update user
Route::put('/user', [UserController::class, 'store']);
//Delete user
Route::delete('/user/{id}', [UserController::class, 'destroy']);