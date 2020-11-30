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

Route::post('register', 'Api\AuthController@register');
Route::post('login', 'Api\AuthController@login');
Route::get('/user/verify/{id}', 'Api\AuthController@verifyEmail');
Route::get('user/image/{location}', 'Api\AuthController@getImage');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return response(['message' => 'Success', 'user' => $request->user()]);
});

Route::group(['middleware' => 'auth:api'], function() {
    Route::get('product', 'Api\ProductController@index');
    Route::get('product/{id}', 'Api\ProductController@show');
    Route::post('product', 'Api\ProductController@store');
    Route::put('product/{id}', 'Api\ProductController@update');
    Route::delete('product/{id}', 'Api\ProductController@destroy');

    Route::delete('/user/{id}', 'Api\AuthController@destroy');
    Route::post('user/image', 'Api\AuthController@uploadImage');
    Route::put('user/{id}', 'Api\AuthController@update');
    Route::post('logout', 'Api\AuthController@logout');
});



