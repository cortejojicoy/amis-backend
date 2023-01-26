<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\Auth\GoogleController;

use App\Http\Controllers\Student\CoiController;
use App\Http\Controllers\Student\CoiTxnController;
use App\Http\Controllers\Student\PrerogController;
use App\Http\Controllers\Student\PrerogTxnController;
use App\Http\Controllers\Student\PcwController;
use App\Http\Controllers\Student\StudentAddMentorController;
use App\Http\Controllers\Student\StudentDetailController;
use App\Http\Controllers\Student\StudentMaTxnController;
use App\Http\Controllers\Student\StudentMaController;
use App\Http\Controllers\Student\StudentActiveMentorController;
use App\Http\Controllers\Student\StudentConfirmController;
use App\Http\Controllers\Student\Program;

use App\Http\Controllers\Faculty\CoiController as FacultyCoiController;
use App\Http\Controllers\Faculty\CoiTxnController as FacultyCoiTxnController;
use App\Http\Controllers\Faculty\PrerogController as FacultyPrerogController;
use App\Http\Controllers\Faculty\PrerogTxnController as FacultyPrerogTxnController;
use App\Http\Controllers\Faculty\PcwController as FacultyPcwController;
use App\Http\Controllers\Faculty\FacultyMaTableController;
use App\Http\Controllers\Faculty\FacultyMaTxnController;
use App\Http\Controllers\Faculty\FacultyMaController;
use App\Http\Controllers\Faculty\FacultyMentorController;

use App\Http\Controllers\Admin\CoiTxnController as AdminCoiTxnController;
use App\Http\Controllers\Admin\PrerogController as AdminPrerogController;
use App\Http\Controllers\Admin\PrerogTxnController as AdminPrerogTxnController;
use App\Http\Controllers\Admin\AdminMaTableController;
use App\Http\Controllers\Admin\AdminMaTxnController;
use App\Http\Controllers\Admin\AdminMaController;

use App\Http\Controllers\SuperAdmin\DownloadController;
use App\Http\Controllers\SuperAdmin\CourseOfferingController as SuperAdminCourseOfferingController;


use App\Http\Controllers\CourseOfferingController;
use App\Http\Controllers\ExternalLinkController;
use App\Http\Controllers\MaController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;

use App\Http\Controllers\CurriculumController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentTermController;

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
Route::group(['middleware' => ['auth:sanctum','role:faculty'],'prefix'=>'faculties', 'as' => 'faculties.'], function () {
    Route::apiResource('coitxns', FacultyCoiTxnController::class);
    Route::apiResource('consent-of-instructors', FacultyCoiController::class);
    Route::apiResource('prerog_txns', FacultyPrerogTxnController::class);
    Route::apiResource('prerogative-enrollments', FacultyPrerogController::class);
    Route::apiResource('matxns', FacultyMaTxnController::class);
    Route::apiResource('ma', FacultyMaController::class);
    Route::apiResource('faculty-ma', FacultyMaTableController::class);
    Route::apiResource('plan-of-courseworks', FacultyPcwController::class);
    Route::apiResource('mentors', FacultyMentorController::class);
});

Route::group(['middleware' => ['auth:sanctum', 'role:student'],'prefix'=>'students', 'as' => 'students.'], function () {
    Route::apiResource('programs', Program::class);
    Route::apiResource('consent-of-instructors', CoiController::class);
    Route::apiResource('coitxns', CoiTxnController::class);
    Route::apiResource('prerogative-enrollments', PrerogController::class);
    Route::apiResource('prerog_txns', PrerogTxnController::class);
    Route::apiResource('plan-of-courseworks', PcwController::class);

    Route::apiResource('matxns', StudentMaTxnController::class);
    Route::apiResource('student-confirm', StudentConfirmController::class);
    // Route::apiResource('student-details', StudentMaController::class);
    // Route::apiResource('student-details', StudentDetailController::class);
    Route::apiResource('{sais_id}/saved-mentors', StudentAddMentorController::class);
    Route::apiResource('{sais_id}/active-mentors', StudentActiveMentorController::class);
    Route::post('{sais_id}/nominated-mentors/collection', [StudentAddMentorController::class, 'bulkUpdate']);
});

Route::group(['middleware' => ['auth:sanctum', 'role:admin'],'prefix'=>'admins', 'as' => 'admins.'], function () {
    Route::apiResource('coitxns', AdminCoiTxnController::class);
    Route::apiResource('prerog_txns', AdminPrerogTxnController::class);
    Route::apiResource('prerogative-enrollments', AdminPrerogController::class);
    Route::apiResource('check-tags', TagController::class);
    Route::apiResource('matxns', AdminMaTxnController::class);
    Route::apiResource('admin-ma', AdminMaTableController::class);
    Route::apiResource('ma', AdminMaController::class);
});

Route::group(['middleware' => ['auth:sanctum', 'role:super_admin'],'prefix'=>'super_admins', 'as' => 'super_admins.'], function () {
    Route::apiResource('{module}/download', DownloadController::class);
    Route::apiResource('permissions', PermissionController::class);
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('tags', TagController::class);
    Route::apiResource('users', UserController::class);
    Route::apiResource('course-offerings', SuperAdminCourseOfferingController::class);
});

//routes open for all roles but needs auth
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::apiResource('mentor-assignments', MaController::class);
    Route::apiResource('student-details', StudentMaController::class);
    Route::apiResource('course-offerings', CourseOfferingController::class);
    Route::apiResource('users', UserController::class);
    Route::apiResource('courses', CourseController::class);
    Route::apiResource('programs', ProgramController::class);
    Route::apiResource('curriculums', CurriculumController::class);
    Route::apiResource('students', StudentController::class);
    Route::apiResource('faculties', FacultyController::class);
    Route::apiResource('student-terms', StudentTermController::class);
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