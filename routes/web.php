<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(['namespace' => 'App\Http\Controllers\Api'], function () {

    Route::get('api/login', 'UserController@login');
    Route::get('api/getalluser', 'UserController@getAllUser');
    Route::post('api/register', 'UserController@register');
    Route::post('api/update_password', 'UserController@updatePassword');
});
