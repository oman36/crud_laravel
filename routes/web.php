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

Route::get('/', 'DataBaseController@tables');
Route::get('/{table}', 'DataBaseController@rows');
Route::get('/{table}/{id}', 'DataBaseController@row');
Route::post('/{table}/{id}', 'DataBaseController@saveRow');
Route::delete('/{table}/{id}', 'DataBaseController@deleteRow');
