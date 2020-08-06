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
Route::get('/web/password/request', function () {return view('passwords.email');});
Route::get('/web/password/reset', function () {return view('passwords.reset');});
Route::post('/web/password/request', 'PublicController@requestResetLink');
Route::post('/web/password/reset', 'PublicController@setupNewPassword');
Route::get('/', function () {return view('welcome');});