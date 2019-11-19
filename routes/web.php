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
    return redirect('/login');
});

Auth::routes();

Route::group(['middleware' => ['auth']], function () {
    Route::group(['prefix' => 'maintenance'], function () {
        Route::get('/', 'MaintenanceController@index')->name('home');
        Route::get('/list', 'MaintenanceController@list')->name('list');
        Route::post('/store', 'MaintenanceController@store')->name('store');
        Route::post('/delete', 'MaintenanceController@delete')->name('delete');
    });
});

