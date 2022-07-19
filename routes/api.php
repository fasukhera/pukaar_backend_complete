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

Route::group(['middleware' => ['auth:api']], function () {
    //Therapist Client Diary
    Route::post('diary/therapist/filters', 'Api\Diary\TherapistDiaryController@index');
    Route::post('diary/{id}/therapist', 'Api\Diary\TherapistDiaryController@edit');
    Route::post('diary/therapist/store', 'Api\Diary\TherapistDiaryController@store');
    Route::post('get-client-diary', 'Api\Diary\TherapistDiaryController@getClientDiary');//new
    //Client Diary
    Route::resource('diary', 'Api\Diary\DiaryController');
    Route::post('get-graph-data', 'Api\Diary\DiaryController@getGraphicDiary');//new
    Route::post('share-diary', 'Api\Diary\DiaryController@shareDiary');//new
    Route::post('diary/filters', 'Api\Diary\DiaryController@index');
    //Session
    Route::resource('/session', 'Api\Session\SessionController');
    
    //Donated Sessions/ sessions
    Route::post('/sessions', 'Api\Session\SessionController@index');

    //OneOnOneSession
    Route::post('onesessionrequest', 'Api\Session\SessionController@oneOnOneSession');
    Route::post('onesessionresponse', 'Api\Session\SessionController@oneOnOneSession');    
    Route::post('one-one-taken', 'Api\Session\SessionController@oneOnOneTaken');//new  
    Route::get('one-one-logs', 'Api\Session\SessionController@oneOnOneLogs');//new
    //Admin Session
    Route::post('/admin/session', 'Api\Session\SessionController@admin_session_index');
    Route::post('/admin/session/update', 'Api\Session\SessionController@admin_session_update');
    Route::post('/admin/session/edit', 'Api\Session\SessionController@admin_session_edit');
    Route::post('/admin/approved-payment-details', 'Api\Session\SessionController@getApprovedPayments');//new
    //Assign Therapist
    Route::post('/available', 'Api\AssignTherapist\AssignTherapistController@available_therapist');
    Route::post('/available/edit/{id}', 'Api\AssignTherapist\AssignTherapistController@edit');
    //assigned by admin
    Route::post('/admin/update/therapist', 'Api\AssignTherapist\AssignTherapistController@forward_therapist');
    //accepted by therapist
    Route::post('/accept/therapist', 'Api\AssignTherapist\AssignTherapistController@accept_therapist');
    //edit Therapist by Admin
    Route::get('/therapist/{id}/edit', 'Api\UserManagement\UserController@therapist_edit');
    Route::post('/therapist/update', 'Api\UserManagement\UserController@therapist_update');
    //change Therapist
    Route::post('/changeTherapist','Api\AssignTherapist\AssignTherapistController@changeTherapist');
    //get change Therapist requests
    Route::get('/changeTherapistRequests','Api\AssignTherapist\AssignTherapistController@getChangetherapistReq');
    //get assigned therapist
    Route::get('getAssignedTherapist', 'Api\AssignTherapist\AssignTherapistController@getAssignedTherapist');
    //get Client Profile
    Route::post('/therapist/get-client-profile', 'Api\AssignTherapist\AssignTherapistController@getClientProfile');//new
    //get assigned patient profies
    Route::get('/admin/get-assigned-patient', 'Api\AssignTherapist\AssignTherapistController@getAssignPatient');//new
    //Bank
    Route::resource('bank', 'Api\Bank\BankController');
    //ClientProfile
    Route::resource('client', 'Api\Client\ProfileController');
    Route::post('/client', 'Api\Client\ProfileController@store');
    //Complete clientProfile update
    Route::post('/client/profile/update', 'Api\Client\ProfileController@update_complete_profile');
    //Packages
    Route::resource('package', 'Api\Package\PackageController');
    //User
    Route::post('/user', 'Api\UserManagement\UserController@index');
    Route::resource('users', 'Api\UserManagement\UserController');
    Route::post('/user/update/{id}', 'Api\UserManagement\UserController@update');
    Route::patch('/user/{id}', 'Api\UserManagement\UserController@destroy');
    //Bio
    Route::resource('/bio', 'Api\Bio\BioController');//new
    Route::post('/bio/update', 'Api\Bio\BioController@update');//new
    //Role
    Route::resource('roles', 'Api\UserManagement\RoleController');
    Route::delete('/roles/delete/{id}', 'Api\UserManagement\RoleController@destroy');
    //Permission
    Route::resource('permissions', 'Api\UserManagement\PermissionController');
    Route::get('/permissions/update/{id}', 'Api\UserManagement\PermissionController@update');
    Route::delete('/permissions/delete/{id}', 'Api\UserManagement\PermissionController@destroy');
    //forum
    Route::get('/forum', 'Api\Forum\ForumController@index');
    Route::post('/post', 'Api\Forum\ForumController@store_post');
    Route::post('/comment', 'Api\Forum\ForumController@store_comment');
    Route::get('/show-comments/{post_id}', 'Api\Forum\ForumController@showComment');//new
//Notification
    Route::post('notify', 'NotificationController@notify');
    Route::get('get-notifications/{user_id}', 'NotificationController@getNotification');
//Chat
    Route::post('send-message', 'MessageController@sendMessage');
    Route::post('get-message', 'MessageController@index');

//Session Log
    Route::post('session-takens', 'Api\Session\SessionController@takenSession');
    Route::get('session-logs', 'Api\Session\SessionController@sessionLogs');
//passcode
    Route::post('set-passcode', 'Api\UserManagement\UserController@setPasscode');//new
    Route::post('passcode', 'Api\UserManagement\UserController@loginWithPasscode');//new

//Display Picture
    Route::post('display-picture', 'Api\UserManagement\UserController@displayPicture');//new
});

//Auth
Route::post('register', 'Api\UserManagement\UserController@register');
Route::post('login', 'Api\UserManagement\UserController@login');
Route::post('/password/reset', 'Api\UserManagement\ForgotPasswordController@send');
Route::post('/set/password', 'Api\UserManagement\ResetPasswordController@update');

Route::post('reset-passcode', 'Api\UserManagement\UserController@passcodeReset');//new
Route::post('forget-passcode', 'Api\UserManagement\UserController@forgetPasscode');//new
Route::post('verify-passcode', 'Api\UserManagement\UserController@verifyPasscode');//new