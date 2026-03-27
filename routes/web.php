<?php

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', 'Auth\LoginController@showLogin')->name('login');
Route::post('/login', 'Auth\LoginController@login');
Route::post('/logout', 'Auth\LoginController@logout'); // ubah ke POST

Route::group(['middleware' => 'auth:superuser'], function () {

    Route::get('/prospect', 'Sales\ProspectController@index');
    Route::get('/prospect/create', 'Sales\ProspectController@create');
    Route::post('/prospect/store', 'Sales\ProspectController@store');

    // PROFILE
    Route::get('/profile', 'Sales\ProfileController@index');
    Route::post('/logout', 'Auth\LoginController@logout');
});