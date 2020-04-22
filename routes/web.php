<?php

use Illuminate\Http\Request;

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


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
    Route::get('inbound-product-summary/filter/{model}',function(Request $request,$route){
        $var = new App\Http\Controllers\Voyager\InboundProductSummaryController();
        return $var->indexByModel($request,$route);
    });
});
