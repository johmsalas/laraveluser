<?php

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
Route::get('/home', 'UserController@index');

Auth::routes();
Route::get('user/activation/{token}', 'Auth\LoginController@activateUser')->name('user.activate');

Route::get('auth/facebook', 'Auth\FacebookAuthController@redirectToProvider');
Route::get('auth/facebook/callback', 'Auth\FacebookAuthController@handleProviderCallback');

Route::group(['middleware' => 'auth'], function () {
    Route::get('users/{id}/delete', ['as' => 'users.delete', 'uses' => 'UserController@destroy']);
    Route::resource('users', 'UserController', ['except' => [
        'create', 'store', 'destroy'
    ]]);

    Route::get('users/export/{format}', ['as' => 'users-export', 'uses' => 'UserController@export']);
    Route::post('users/import', ['as' => 'users-import', 'uses' => 'UserController@import']);
});
