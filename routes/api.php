<?php

use App\Http\Controllers\Admin\AdminPrerogController;
use App\Http\Controllers\Admin\AdminPrerogTxnController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\CourseOfferingController;
use App\Http\Controllers\ExternalLinkController;
use App\Http\Controllers\Faculty\AdviserController;
use App\Http\Controllers\Student\SaveMentorController;
use App\Http\Controllers\Student\Program;
use App\Http\Controllers\Faculty\BasicInfoController;
use App\Http\Controllers\Faculty\FacultyCoiController;
use App\Http\Controllers\Faculty\FacultyCoiTxnController;
use App\Http\Controllers\Faculty\FacultyPrerogController;
use App\Http\Controllers\Faculty\FacultyPrerogTxnController;
use App\Http\Controllers\Student\StudentCoiController;
use App\Http\Controllers\Student\StudentCoiTxnController;
use App\Http\Controllers\Student\StudentPrerogController;
use App\Http\Controllers\Student\StudentPrerogTxnController;

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
// Route::apiResource('faculties', BasicInfoController::class);
Route::group(['middleware' => ['auth:sanctum','role:faculty'],'prefix'=>'faculties'], function () {
    Route::apiResource('advisees', AdviserController::class);
    Route::apiResource('mentor-assignments', AdviserController::class);
    Route::apiResource('coitxns', FacultyCoiTxnController::class);
    Route::apiResource('consent-of-instructors', FacultyCoiController::class);
    Route::apiResource('prerog_txns', FacultyPrerogTxnController::class);
    Route::apiResource('prerogative-enrollments', FacultyPrerogController::class);
});

Route::group(['middleware' => ['auth:sanctum', 'role:student'],'prefix'=>'students'], function () {
    Route::post('{sais_id}/nominated-mentors/collection', [SaveMentorController::class, 'bulkUpdate']);
    Route::apiResource('{sais_id}/nominated-mentors', SaveMentorController::class);
    Route::apiResource('programs', Program::class);
    Route::apiResource('consent-of-instructors', StudentCoiController::class);
    Route::apiResource('coitxns', StudentCoiTxnController::class);
    Route::apiResource('prerogative-enrollments', StudentPrerogController::class);
    Route::apiResource('prerog_txns', StudentPrerogTxnController::class);
});

Route::group(['middleware' => ['auth:sanctum', 'role:admin'],'prefix'=>'admins'], function () {
    Route::apiResource('prerog_txns', AdminPrerogTxnController::class);
    Route::apiResource('prerogative-enrollments', AdminPrerogController::class);
});

//routes open for all roles but needs auth
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::apiResource('course-offerings', CourseOfferingController::class);
    Route::apiResource('users', UserController::class);
});

Route::apiResource('{action}/external_links', ExternalLinkController::class);

//List users
// Route::get('/users', [UserController::class, 'index']);
// //List single user
// Route::get('/user/{id}', [UserController::class, 'show']);
// //Create new user
// Route::post('/user', [UserController::class, 'store']);
// //Update user
// Route::put('/user', [UserController::class, 'store']);
// //Delete user
// Route::delete('/user/{id}', [UserController::class, 'destroy']);