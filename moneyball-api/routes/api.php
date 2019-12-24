<?php

use Illuminate\Http\Request;

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

Route::group(['middleware' => ['guest:api']], function () {
    Route::group(['prefix' => 'users'], function () {
        Route::post('/register', 'Auth\LoginController@register');
        Route::post('/validate-register', 'Auth\LoginController@validateRegister');
        Route::post('/login', 'Auth\LoginController@login');
        Route::post('/username', 'Auth\LoginController@existUsername');
    });

    Route::post('/confirmToken', 'UserController@changeEmail');
    Route::post('/sendToken', 'UserController@sendToken');
    Route::post('/resetPassword', 'UserController@changePassword');
});

Route::group(['middleware' => ['auth:api', 'verified']], function () {
    // Users
    Route::group(['prefix' => 'users'], function () {
        Route::get('/', 'UserController@view');
        Route::get('/list', 'UserController@index');
        Route::post('/update', 'UserController@update');
        Route::post('/connect-onesignal', 'UserController@connectOnesignal');
        Route::post('/logout', 'Auth\LoginController@logout');
        Route::post('/refresh', 'Auth\LoginController@refresh');
        Route::post('/changePassword', 'UserController@changePassword');
        Route::post('/sendToken', 'UserController@sendToken');
        Route::post('/changeEmail', 'UserController@changeEmail');
        Route::get('/leader-board', 'UserController@leaderBoard');

        Route::post('/level/complete', 'UserController@completeLevel');
        Route::post('/level/fail', 'UserController@failLevel');
    });

    // Payouts
    Route::group(['prefix' => 'payment'], function () {
        Route::get('/transactions', 'PaymentController@index');

        Route::post('/unlock', 'PaymentController@unlock');
        Route::post('/unlockinapp', 'PaymentController@unlockinapp');
        Route::post('/deposit', 'PaymentController@deposit');
        Route::post('/chips', 'PaymentController@chips');
        Route::post('/package', 'PaymentController@package');
        Route::post('/redeem', 'PaymentController@redeem');
        Route::post('/withdraw', 'PaymentController@withdraw');
    });

    // Games
    Route::group(['prefix' => 'game'], function () {
        Route::post('/start', 'GameController@start');
        Route::post('/end', 'GameController@end');
    });

    // Raffles
    Route::group(['prefix' => 'raffles'], function () {
        Route::get('/', 'RaffleController@index');
        Route::get('/current', 'RaffleController@current');
        Route::get('/{raffle}', 'RaffleController@view');
    });
});
