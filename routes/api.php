<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\CoiTxnController;
use App\Http\Controllers\CourseOfferingController;
use App\Http\Controllers\Faculty\AdviserController;
use App\Http\Controllers\Student\SaveMentorController;
use App\Http\Controllers\Student\Program;
use App\Http\Controllers\Faculty\BasicInfoController;
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

//faculty
Route::apiResource('faculties', BasicInfoController::class);
Route::group(['middleware' => ['auth:sanctum'],'prefix'=>'faculties'], function () {
    Route::apiResource('advisees', AdviserController::class);
    Route::apiResource('mentor-assignments', AdviserController::class);
    
});

Route::group(['middleware' => ['auth:sanctum'],'prefix'=>'students'], function () {
    Route::post('{saisid}/nominated-mentors/collection', [SaveMentorController::class, 'bulkUpdate']);
    Route::apiResource('{saisid}/nominated-mentors', SaveMentorController::class);
    Route::apiResource('programs', Program::class);
});

//routes open for all roles but needs auth
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('course-offerings/get-sections', [CourseOfferingController::class, 'getSections']);
    Route::apiResource('course-offerings', CourseOfferingController::class);
});

//txn history resources
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::apiResource('coitxn', CoiTxnController::class);
});


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