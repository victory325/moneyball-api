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

// For testing purposes
/*Route::get('instagram/connect', 'UserController@connectInstagram')->name('instagram.connect');
Route::get('instagram/exchange', 'UserController@exchangeInstagram')->name('instagram.exchange');
Route::get('facebook/connect', 'UserController@connectFacebook')->name('facebook.connect');
Route::get('facebook/exchange', 'UserController@exchangeFacebook')->name('facebook.exchange');*/

