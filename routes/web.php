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

Route::get('/tracking', 'HomeController@trackingtest');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
//Notification
Route::post('notify', 'NotificationController@notify');
Route::get('get-notifications/{user_id}', 'NotificationController@getNotification');
//Chat
Route::post('send-message', 'MessageController@sendMessage');
