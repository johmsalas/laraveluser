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

Auth::routes();

Route::get('/home', 'HomeController@index');

Route::get('auth/facebook', 'Auth\FacebookAuthController@redirectToProvider');
Route::get('auth/facebook/callback', 'Auth\FacebookAuthController@handleProviderCallback');

Route::get('user/activation/{token}', 'Auth\LoginController@activateUser')->name('user.activate');

Route::get('users/{id}/delete', ['as' => 'users.delete', 'uses' => 'UserController@destroy']);
Route::resource('users', 'UserController', ['except' => [
    'create', 'store', 'destroy'
]]);
