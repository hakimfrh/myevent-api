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
    Route::get('api/continuegoogle', 'UserController@continueGoogle');
    Route::get('api/getalluser', 'UserController@getAllUser');
    Route::post('api/register', 'UserController@register');
    Route::post('api/update_password', 'UserController@updatePassword');
    Route::get('api/sendcode', 'UserController@sendCode'); //kurang
    
    
    Route::get('api/event', 'EventController@getAllEvent');
    Route::get('api/event/getEvent', 'EventController@getEvent');
    Route::get('api/event/isEnrolled', 'EventController@isEnrolled');
    Route::get('api/event/getBooth', 'EventController@getBooth');
    Route::get('api/event/getBoothRange', 'EventController@getBoothRange');
    Route::get('api/event/getBoothTotal', 'EventController@getBoothTotal');
    Route::get('api/event/getBoothAvailable', 'EventController@getBoothAvailable');

    Route::post('api/order/makeOrder', 'OrderController@makeOrder');
    Route::post('api/order/uploadBayar', 'OrderController@uploadBayar');
    Route::get('api/order/getOrder', 'OrderController@getOrder');
    Route::get('api/order/getCountOrder', 'OrderController@getCountOrder');
    Route::get('api/order/getOrderedEvent', 'OrderController@getOrderedEvent');
    
    Route::get('api/image', 'ImageController@getImageBase64');
    
});
