<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
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
    return view('index');
});
/*Ruta predefinida temporal con el
partian form.blade donde enviamos datos de prueba*/
Route::get('/transaction/create', 'TransactionController@create');
//Ruta para guardar la transaccion, aqui es donde se realiza la simulacion de como si fuemaos un negocio directo con PAYME
Route::post('/transaction', 'TransactionController@store');
//Ruta que se responde al negocio comercio afiliado, se le responde con datos para que el lo muestre en su ecommerce
Route::post('/transaction/response', 'TransactionController@response');
//Ruta donde se cancela la transaccion
Route::post('/transaction/cancel', 'TransactionController@cancelTransaction');

Route::post('/client/response', 'ClientController@response');
Route::get('/client/redirect', 'ClientController@redirect');

Route::get('/transaction/tigo/create', 'TransactionTigoController@create');
Route::post('/transaction/tigo', 'TransactionTigoController@store');
Route::post('/transaction/tigo/pay', 'TransactionTigoController@payTransaction');
Route::post('/transaction/tigo/check', 'TransactionTigoController@checkTransaction');

// prueba de commit
Route::get('/pruebaenlaceecommercetoapi', function () {
    return view('tunelpasarela');
});
