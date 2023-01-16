<?php

use App\Http\Controllers\API\ActivityController;
use App\Http\Controllers\API\EmployeeController;
use App\Http\Controllers\API\ExpenseController;
use App\Http\Controllers\API\ProgramController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Auth API
Route::name('auth.')->controller(UserController::class)->group(function () {
    Route::post('login', 'login')->name('login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', 'logout')->name('logout');
    });
});

// User API
Route::prefix('user')->middleware('auth:sanctum')
    ->controller(UserController::class)->name('user.')->group(function () {
        Route::post('', 'register')->name('register');
        Route::post('update/{id}', 'update')->name('update');
        Route::post('updateStatus/{id}', 'updateStatus')->name('updateStatus');
        Route::get('', 'fetch')->name('fetch');
    });

// Employee API
Route::prefix('employee')->middleware('auth:sanctum')
    ->controller(EmployeeController::class)->name('employee.')->group(function () {
        Route::get('', 'fetch')->name('fetch');
        Route::post('', 'create')->name('create');
        Route::post('update/{id}', 'update')->name('update');
        Route::post('updateStatus/{id}', 'updateStatus')->name('updateStatus');
        Route::delete('{id}', 'destroy')->name('delete');
    });

// Program API
Route::prefix('program')->middleware('auth:sanctum')
    ->controller(ProgramController::class)->name('program.')->group(function () {
        Route::get('', 'fetch')->name('fetch');
        Route::post('', 'create')->name('create');
        Route::post('update/{id}', 'update')->name('update');
        Route::delete('{id}', 'destroy')->name('delete');
    });

// Activity API
Route::prefix('activity')->middleware('auth:sanctum')
    ->controller(ActivityController::class)->name('activity.')->group(function () {
        Route::get('', 'fetch')->name('fetch');
        Route::post('', 'create')->name('create');
        Route::post('update/{id}', 'update')->name('update');
        Route::delete('{id}', 'destroy')->name('delete');
    });

// Expense API
Route::prefix('expense')->middleware('auth:sanctum')
    ->controller(ExpenseController::class)->name('expense.')->group(function () {
        Route::get('', 'fetch')->name('fetch');
        Route::post('', 'create')->name('create');
        Route::post('update/{id}', 'update')->name('update');
        Route::delete('{id}', 'destroy')->name('delete');
    });