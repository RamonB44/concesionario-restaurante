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
    return view('welcome');
});

Auth::routes();

Route::get('/home', function() {
    return view('home');
})->name('home')->middleware('auth');

Route::prefix('employes')->group(function () {
    Route::get('/', 'EmployesController@index')->name('employes.index');
    Route::get('/getTable','EmployesController@getTable')->name('employes.getTable');
    Route::get('/create','EmployesController@create')->name('employes.create');
    Route::post('/create','EmployesController@create')->name('employes.create');
    Route::get('/update/{id}','EmployesController@update')->name('employes.update');
    Route::post('/update/{id}','EmployesController@update')->name('employes.update');
    Route::get('/delete/{id}','EmployesController@delete')->name('employes.delete');
    Route::get('/code/{code}','EmployesController@getEmploye')->name('employes.getEmploye');
    Route::get('/generateCode/{id}','EmployesController@generateNewCode')->name('employes.generateCode');
    Route::post('/import','EmployesController@importEmploye')->name('employes.import');
});


Route::prefix('products')->group(function () {
    Route::get('/', 'ProductController@index')->name('products.index');
    Route::get('/getTable','ProductController@getTable')->name('products.getTable');
    Route::get('/create','ProductController@create')->name('products.create');
    Route::post('/create','ProductController@create')->name('products.create');
    Route::get('/update/{id}','ProductController@update')->name('products.update');
    Route::post('/update/{id}','ProductController@update')->name('products.update');
    Route::get('/delete/{id}','ProductController@delete')->name('products.delete');
    Route::get('/getbyId/{id}','ProductController@getbyId')->name('products.getbyId');
    Route::post('/printBarcode','EmployesController@printBarcode')->name('employes.barcode');
});


Route::prefix('ingredient')->group(function () {
    Route::get('/', 'IngredientController@index')->name('ingredient.index');
    Route::get('/getTable','IngredientController@getTable')->name('ingredient.getTable');
    Route::get('/getData','IngredientController@getData')->name('ingredient.getData');
    Route::get('/create','IngredientController@create')->name('ingredient.create');
    Route::post('/create','IngredientController@create')->name('ingredient.create');
    Route::get('/update/{id}','IngredientController@update')->name('ingredient.update');
    Route::post('/update/{id}','IngredientController@update')->name('ingredient.update');
    Route::get('/delete/{id}','IngredientController@delete')->name('ingredient.delete');
});


Route::prefix('stock')->group(function () {
    Route::get('/', 'StockController@index')->name('stock.index');

    Route::get('/getTable','StockController@getTable')->name('stock.getTable');

    Route::post('/regulate','StockController@regulate')->name('stock.regulate');

    Route::get('/regulate','StockController@regulate')->name('stock.regulate');

});

Route::prefix('orders')->group(function () {
    Route::get('/', 'OrderController@index')->name('orders.index');

    Route::get('/getOrder','OrderController@getTable')->name('orders.getTable');

    Route::get('/sendOrder','OrderController@submitOrden')->name('orders.submitOrden');

    Route::post('/sendOrder','OrderController@submitOrden')->name('orders.submitOrden');

    Route::get('/anular/{id}','OrderController@deleteOrden')->name('orders.anular');

    Route::get('/searchByEmploye/{codigo}/{start}/{end}','OrderController@searchByCodigo')->name('orders.search');
});

Route::prefix('query')->group(function(){
    Route::get('/','HomeController@showQuery')->name('query.index');
});

Route::prefix('xlsx')->group(function(){
    Route::get('/orden/getxlsxtoday','ExcelController@getXlsx')->name('xlsx.getData');
});
