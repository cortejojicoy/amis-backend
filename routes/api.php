<?php

use App\Http\Controllers\Auth\GoogleController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\Student\Program;
use App\Http\Controllers\Student\StudentAddMentorController;
use App\Http\Controllers\Student\StudentDetailController;
use App\Http\Controllers\Student\StudentMaTxnController;
use App\Http\Controllers\Student\StudentActiveMentorController;
use App\Http\Controllers\Student\StudentConfirmController;
use App\Http\Controllers\Student\SaveMentorController;
use App\Http\Controllers\Student\StudentCoiController;
use App\Http\Controllers\Student\StudentCoiTxnController;
use App\Http\Controllers\Student\StudentPrerogController;
use App\Http\Controllers\Student\StudentPrerogTxnController;

// use App\Http\Controllers\Faculty\AdviserController;
use App\Http\Controllers\Faculty\BasicInfoController;
use App\Http\Controllers\Faculty\FacultyCoiController;
use App\Http\Controllers\Faculty\FacultyCoiTxnController;
use App\Http\Controllers\Faculty\FacultyPrerogController;
use App\Http\Controllers\Faculty\FacultyPrerogTxnController;
use App\Http\Controllers\Faculty\FacultyMaTableController;
use App\Http\Controllers\Faculty\FacultyMaTxnController;
use App\Http\Controllers\Faculty\FacultyMaController;

use App\Http\Controllers\Admin\AdminCoiTxnController;
use App\Http\Controllers\Admin\AdminPrerogController;
use App\Http\Controllers\Admin\AdminPrerogTxnController;
use App\Http\Controllers\Admin\AdminMaTableController;
use App\Http\Controllers\Admin\AdminMaTxnController;
use App\Http\Controllers\Admin\AdminMaController;

use App\Http\Controllers\SuperAdmin\DownloadController;

use App\Http\Controllers\CourseOfferingController;
use App\Http\Controllers\ExternalLinkController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\ReqMentorController;
use App\Http\Controllers\MaController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;


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
Route::group(['middleware' => ['auth:sanctum','role:faculty'],'prefix'=>'faculties', 'as' => 'faculties.'], function () {
    // Route::apiResource('advisees', AdviserController::class);
    // Route::apiResource('mentor-assignments', AdviserController::class);
    Route::apiResource('coitxns', FacultyCoiTxnController::class);
    Route::apiResource('consent-of-instructors', FacultyCoiController::class);
    Route::apiResource('prerog_txns', FacultyPrerogTxnController::class);
    Route::apiResource('prerogative-enrollments', FacultyPrerogController::class);
    Route::apiResource('matxns', FacultyMaTxnController::class);
    Route::apiResource('ma', FacultyMaController::class);
    Route::apiResource('faculty-ma', FacultyMaTableController::class);

});

Route::group(['middleware' => ['auth:sanctum', 'role:student'],'prefix'=>'students', 'as' => 'students.'], function () {
    Route::apiResource('programs', Program::class);
    Route::apiResource('consent-of-instructors', StudentCoiController::class);
    Route::apiResource('coitxns', StudentCoiTxnController::class);
    Route::apiResource('prerogative-enrollments', StudentPrerogController::class);
    Route::apiResource('prerog_txns', StudentPrerogTxnController::class);

    Route::apiResource('matxns', StudentMaTxnController::class);
    Route::apiResource('student-confirm', StudentConfirmController::class);
    Route::apiResource('student-details', StudentDetailController::class);
    // Route::apiResource('saved-mentors', StudentAddMentorController::class);
    Route::apiResource('{sais_id}/saved-mentors', StudentAddMentorController::class);
    Route::apiResource('{sais_id}/active-mentors', StudentActiveMentorController::class);
    Route::post('{sais_id}/nominated-mentors/collection', [StudentAddMentorController::class, 'bulkUpdate']);
});

Route::group(['middleware' => ['auth:sanctum', 'role:admin'],'prefix'=>'admins', 'as' => 'admins.'], function () {
    Route::apiResource('coitxns', AdminCoiTxnController::class);
    Route::apiResource('prerog_txns', AdminPrerogTxnController::class);
    Route::apiResource('prerogative-enrollments', AdminPrerogController::class);
    Route::apiResource('matxns', AdminMaTxnController::class);
    Route::apiResource('admin-ma', AdminMaTableController::class);
    Route::apiResource('ma', AdminMaController::class);
});

Route::group(['middleware' => ['auth:sanctum', 'role:super_admin'],'prefix'=>'super_admins', 'as' => 'super_admins.'], function () {
    Route::apiResource('{module}/download', DownloadController::class);
});

//routes open for all roles but needs auth
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::apiResource('mentor-assignments', MaController::class);
    Route::apiResource('course-offerings', CourseOfferingController::class);
    Route::apiResource('users', UserController::class);
    Route::apiResource('faculties', FacultyController::class);
    Route::apiResource('check-tags', TagController::class);
    Route::get('student-info', [StudentDetailController::class, 'getStudentById']);
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