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

/**
 * LOGIN ROUTE
 */
Route::group([
    'as' => 'auth.login.',
    'namespace' => 'App\Http\Controllers\Auth',
    'prefix' => 'login'
], function () {
    Route::get('/', 'LoginController@index')->name('index');
    Route::post('/', 'LoginController@login')->name('login');
    Route::get('/login2fa', 'LoginController@index2fa')->name('index2fa')->middleware('auth.normal');
    Route::post('/login2fa', 'LoginController@login2fa')->name('login2fa')->middleware('auth.normal');
});

/**
 * REGISTER ROUTE
 */
Route::group([
    'as' => 'auth.register.',
    'namespace' => 'App\Http\Controllers\Auth',
    'prefix' => 'register',
], function () {
    Route::get('/', 'RegisterController@index')->name('index');
    Route::post('/', 'RegisterController@register')->name('register');
    Route::get('/verify/{id}/{token}', 'RegisterController@verify')->name('verify');
});

/**
 * LOGOUT ROUTE
 */

Route::post('/auth/logout', ['App\Http\Controllers\Auth\LogoutController', 'logout'])->name('auth.logout');

/**
 * USER ROUTE
 */
Route::group([
    'as' => 'account.',
    'namespace' => 'App\Http\Controllers\Account',
    'middleware' => 'auth.advanced',
    'prefix' => 'account'
], function () {
    Route::get('dashboard', 'DashboardController@index')->name('dashboard.index');

    Route::get('security', 'SecurityController@index')->name('security.index');
    Route::get('setup-google2fa', 'SecurityController@setupGoogle2FA')->name('security.setup-google2fa');
    Route::post('setup-google2fa', 'SecurityController@verifySetupGoogle2FA')->name('security.verify-setup-google2fa');
});
