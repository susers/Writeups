<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', function () { return view('welcome'); });
Auth::routes();
Route::get('/home', 'HomeController@index');
Route::get('/note', 'NoteController@index')->name('note');
Route::get('/upload', 'UploadController@index')->name('upload');
Route::post('/upload', 'UploadController@upload')->name('upload');
Route::get('/flag', 'FlagController@showFlag')->name('flag');
Route::get('/files', 'UploadController@files')->name('files');
Route::post('/check', 'UploadController@check')->name('check');
Route::get('/error', 'HomeController@error')->name('error');