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
Route::middleware('auth:api')->post('logout', 'Api\AuthController@logout');
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// SAYA GOBLOK MAKANYA BUAT 2 GROUP

Route::group(['middleware' => 'auth:api'], function() {
    Route::get('product', 'Api\ProductController@index');
    Route::get('product/{id}', 'Api\ProductController@show');
    Route::post('product', 'Api\ProductController@store');
    Route::put('product/{id}', 'Api\ProductController@update');
    Route::delete('product/{id}', 'Api\ProductController@destroy');
});

Route::group(['middleware' => 'api'], function() {
    Route::get('pbp/product', 'Api\ProductController@index');
    Route::get('pbp/product/{id}', 'Api\ProductController@show');
    Route::post('pbp/product', 'Api\ProductController@store');
    Route::put('pbp/product/{id}', 'Api\ProductController@update');
    Route::delete('pbp/product/{id}', 'Api\ProductController@destroy');
});
