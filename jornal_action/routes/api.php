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

Route::get('/generate-key-pair', 'KeyPairController@generateKeyPair');

Route::post('/auth/register', [\App\Http\Controllers\AuthController::class, 'register']);
Route::post('/auth/login', [\App\Http\Controllers\AuthController::class, 'login']);

Route::middleware('jwt.auth')
    ->group(function () {
        Route::post('/auth/logout', [\App\Http\Controllers\AuthController::class, 'logout']);
        Route::get('/auth/user', [\App\Http\Controllers\AuthController::class, 'user']);

        Route::resource('actions', \App\Http\Controllers\ActionController::class)
            ->only(['index', 'store']);
        Route::post('/actions/filter', [\App\Http\Controllers\ActionController::class, 'filter'])
            ->name('actions.filter');
    });
