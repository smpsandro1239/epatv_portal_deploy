<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

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

// Rotas públicas
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/register/company', [AuthController::class, 'registerCompany']);
Route::get('/registration-window/status', 'App\Http\Controllers\RegistrationWindowController@status');
Route::get('/jobs', 'App\Http\Controllers\JobController@index');
Route::get('/jobs/{job}', 'App\Http\Controllers\JobController@show');
Route::get('/areas-of-interest', 'App\Http\Controllers\AreaOfInterestController@index');

// Rotas protegidas
Route::middleware('auth:sanctum')->group(function () {
    // Rotas comuns a todos os utilizadores autenticados
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // Rotas para ex-alunos
    Route::middleware('role:student')->group(function () {
        Route::post('/jobs/{job}/apply', 'App\Http\Controllers\JobApplicationController@store');
        Route::get('/user/applications', 'App\Http\Controllers\JobApplicationController@userApplications');
        Route::delete('/applications/{application}', 'App\Http\Controllers\JobApplicationController@destroy');
        Route::post('/jobs/{job}/save', 'App\Http\Controllers\JobController@saveJob');
        Route::delete('/jobs/{job}/unsave', 'App\Http\Controllers\JobController@unsaveJob');
        Route::get('/user/saved-jobs', 'App\Http\Controllers\JobController@savedJobs');
        Route::put('/user/profile', 'App\Http\Controllers\UserController@updateProfile');
    });
    
    // Rotas para empresas
    Route::middleware('role:admin')->group(function () {
        Route::post('/jobs', 'App\Http\Controllers\JobController@store');
        Route::put('/jobs/{job}', 'App\Http\Controllers\JobController@update');
        Route::delete('/jobs/{job}', 'App\Http\Controllers\JobController@destroy');
        Route::get('/company/jobs', 'App\Http\Controllers\JobController@companyJobs');
        Route::get('/company/applications', 'App\Http\Controllers\JobApplicationController@companyApplications');
        Route::put('/applications/{application}/status', 'App\Http\Controllers\JobApplicationController@updateStatus');
        Route::put('/company/profile', 'App\Http\Controllers\UserController@updateCompanyProfile');
    });
    
    // Rotas para superadmin
    Route::middleware('role:superadmin')->group(function () {
        Route::get('/admin/dashboard', 'App\Http\Controllers\AdminController@dashboard');
        Route::get('/admin/users', 'App\Http\Controllers\AdminController@users');
        Route::get('/admin/users/pending', 'App\Http\Controllers\AdminController@pendingUsers');
        Route::put('/admin/users/{user}/approve', 'App\Http\Controllers\AdminController@approveUser');
        Route::put('/admin/users/{user}/reject', 'App\Http\Controllers\AdminController@rejectUser');
        Route::delete('/admin/users/{user}', 'App\Http\Controllers\AdminController@destroyUser');
        Route::get('/admin/jobs', 'App\Http\Controllers\AdminController@jobs');
        Route::delete('/admin/jobs/{job}', 'App\Http\Controllers\AdminController@destroyJob');
        Route::post('/admin/registration-windows', 'App\Http\Controllers\RegistrationWindowController@store');
        Route::put('/admin/registration-windows/{window}', 'App\Http\Controllers\RegistrationWindowController@update');
        Route::get('/admin/registration-windows', 'App\Http\Controllers\RegistrationWindowController@index');
        Route::get('/admin/registration-windows/{window}', 'App\Http\Controllers\RegistrationWindowController@show');
        Route::delete('/admin/registration-windows/{window}', 'App\Http\Controllers\RegistrationWindowController@destroy');
    });
});
