<?php


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


Route::group(['middleware' => 'auth:sanctum'], function () {


    Route::get('lead/{id}',[App\Http\Controllers\CandidateController::class,'show'])->name('leads.show');

    Route::get('leads',[App\Http\Controllers\CandidateController::class,'index'])
        ->middleware('role:agent,manager')->name('leads.index');

    Route::post('leads', [App\Http\Controllers\CandidateController::class, 'store'])
    ->middleware('role:manager')->name('leads.store');


});

Route::post('auth', [App\Http\Controllers\LoginController::class, 'login'])->name('login');
