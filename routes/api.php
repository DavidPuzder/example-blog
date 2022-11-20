<?php

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

Route::group(['namespace' => 'App\Http\Controllers\v1'], function () {
    /* Auth */
    Route::post('/login', 'SanctumController@login');
    Route::post('/register', 'SanctumController@register');
    Route::post('/password-email', 'SanctumController@changePassword');
});

Route::group(['namespace' => 'App\Http\Controllers\v1', 'middleware' => 'auth:sanctum'], function () {
    /* Posts */
    Route::get('/posts', 'PostController@index');
    Route::get('/posts/{id}', 'PostController@show');
});
