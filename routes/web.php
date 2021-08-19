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
Route::post('/auth/logout', ['App\Http\Controllers\Auth\LogoutController', 'logout'])
    ->middleware('auth.advanced')
    ->name('auth.logout');

/**
 * CONFIRM PASSWORD ROUTE
 */
Route::group([
    'as' => 'auth.',
    'namespace' => 'App\Http\Controllers\Auth',
    'prefix' => 'confirm-password',
    'middleware' => 'auth.advanced'
], function () {
    Route::get('/', 'ConfirmPasswordController@index')->name('confirm-password.index');
    Route::post('/', 'ConfirmPasswordController@confirm')->name('confirm-password');
});

/**
 * ACCOUNT ROUTE
 */
Route::group([
    'as' => 'account.',
    'namespace' => 'App\Http\Controllers\Account',
    'middleware' => 'auth.advanced',
    'prefix' => 'account'
], function () {
    Route::get('dashboard', 'DashboardController@index')->name('dashboard.index');
    Route::get('security', 'SecurityController@index')->name('security.index');

    Route::group([
        'middleware' => 'password.confirmed',
    ], function () {
        Route::get('setup-google2fa', 'SecurityController@setupGoogle2FA')
            ->middleware('password.confirmed')
            ->name('security.setup-google2fa');
        Route::post('setup-google2fa', 'SecurityController@verifySetupGoogle2FA')
            ->middleware('password.confirmed')
            ->name('security.verify-setup-google2fa');
        Route::post('turn-off-google2fa', 'SecurityController@turnOffGoogle2FA')
            ->middleware('password.confirmed')
            ->name('security.turn-off-google2fa');
    
        Route::get('view-backup-code', 'SecurityController@viewBackupCode')
            ->middleware('password.confirmed')
            ->name('security.view-backup-code');
        Route::get('download-backup-code', 'SecurityController@downloadBackupCodes')
            ->middleware('password.confirmed')
            ->name('security.download-backup-code');
        });
});
