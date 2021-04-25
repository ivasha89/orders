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

Route::get('/tarifs', 'SpaController@tarifs');
Route::get('/orders', 'SpaController@orders');
Route::post('/makeorder', 'SpaController@makeOrder');
Route::get('/{any}', 'SpaController@index')->where('any', '.*');