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
    'namespace' => 'App\Http\Controllers\Auth'
], function () {
    Route::get('/login', 'LoginController@index')->name('index');
    Route::post('/login', 'LoginController@login')->name('login');
    Route::get('/login/login2fa', 'LoginController@index2fa')->name('index2fa')->middleware('auth.normal');
    Route::post('/login/login2fa', 'LoginController@login2fa')->name('login2fa')->middleware('auth.normal');
});

/**
 * REGISTER ROUTE
 */

/**
 * USER ROUTE
 */
Route::group([
    'as' => 'user.',
    'namespace' => 'App\Http\Controllers\User',
    'middleware' => 'auth.advanced',
], function () {
    Route::get('/user/dashboard', 'DashboardController@index')->name('dashboard.index');
});
