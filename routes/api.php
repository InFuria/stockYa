<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('create', function (){

    dd(csrf_token());
    $product = \App\Product::where('id', 101)->first();
    return view('create', compact('product'));
});

/** PASSPORT **/
Route::group(['prefix' => 'auth'], function () {
    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signup');

    Route::group(['middleware' => 'auth:api'], function() {
        Route::get('logout', 'AuthController@logout');
        Route::get('user', 'AuthController@user');
    });
});

/** PRODUCTS ROUTES  **/
Route::get('products/index', 'Api\ProductController@index');

Route::get('products', 'Api\ProductController@getProducts')->name('products.getProducts');
Route::post('products', 'Api\ProductController@store')->name('products.store');
Route::get('products/select/{product}', 'Api\ProductController@select')->name('products.select');
Route::put('products/{product}', 'Api\ProductController@update')->name('products.update');
Route::patch('products/{product}', 'Api\ProductController@update')->name('products.update');
Route::delete('products/{product}', 'Api\ProductController@destroy')->name('products.destroy');
Route::post('products/{product}/status', 'Api\ProductController@status')->name('products.status');
Route::post('products/{product}/setScore', 'Api\ProductController@setScore')->name('products.setScore');


/** COMPANIES ROUTES **/
Route::get('companies', 'Api\CompanyController@getCompanies')->name('companies.getCompanies');
Route::get('categories', 'Api\CompanyController@listCategories')->name('categories.listCategories');

