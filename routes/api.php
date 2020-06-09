<?php

use App\Helpers;
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

// Ruta de carga de datos provisional xD
Route::get('products/index', 'Api\ProductController@index');

/** PUBLIC ROUTES */
Route::get('products', 'Api\ProductController@getProducts');
Route::get('products/{product}', 'Api\ProductController@select');
Route::get('companies', 'Api\CompanyController@getCompanies');
Route::get('companies/{company}', 'Api\CompanyController@select');
Route::get('categories/company', 'Api\CompanyCategoryController@getCategories');
Route::get('categories/company/{category}', 'Api\CompanyCategoryController@select');
Route::get('categories/products', 'Api\ProductCategoryController@getCategories');
Route::get('categories/products/{category}', 'Api\ProductCategoryController@select');

Route::post('products/{product}/visit', 'Api\ProductController@visits');
Route::post('companies/{company}/visit', 'Api\CompanyController@visits');


Route::middleware(['auth:api'])->group(function () {

    /** USER ROUTES */
    Route::post('users','Api\UserController@store');
    Route::put('users/{user}','Api\UserController@update');
    Route::patch('users/{user}','Api\UserController@update');
    Route::delete('users/{user}','Api\UserController@destroy');
    Route::post('users/{user}/ban','Api\UserController@ban');

    /** PRODUCTS ROUTES  **/
    Route::post('products', 'Api\ProductController@store');
    Route::put('products/{product}', 'Api\ProductController@update');
    Route::patch('products/{product}', 'Api\ProductController@update');
    Route::delete('products/{product}', 'Api\ProductController@destroy');
    Route::post('products/{product}/status', 'Api\ProductController@status');
    Route::post('products/{product}/setScore', 'Api\ProductController@setScore');
    Route::post('products/{product}/setTags', 'Api\ProductController@setTags');

    /** COMPANIES ROUTES **/
    Route::post('companies', 'Api\CompanyController@store');
    Route::put('companies/{company}', 'Api\CompanyController@update');
    Route::patch('companies/{company}', 'Api\CompanyController@update');
    Route::delete('companies/{company}', 'Api\CompanyController@destroy');
    Route::post('companies/{company}/status', 'Api\CompanyController@status');

    Route::post('categories/company', 'Api\CompanyCategoryController@store');
    Route::put('categories/company/{category}', 'Api\CompanyCategoryController@update');
    Route::patch('categories/company/{category}', 'Api\CompanyCategoryController@update');
    Route::delete('categories/company/{category}', 'Api\CompanyCategoryController@destroy');

    Route::post('categories/products', 'Api\ProductCategoryController@store');
    Route::put('categories/products/{product}', 'Api\ProductCategoryController@update');
    Route::patch('categories/products/{product}', 'Api\ProductCategoryController@update');
    Route::delete('categories/products/{product}', 'Api\ProductCategoryController@destroy');

    /** WEB SALES ROUTES **/
    Route::post('websales','Api\WebSaleController@store');
    Route::middleware(['check.access'])->group(function () {
        Route::get('websales/tracking','Api\WebSaleController@tracking');
        Route::get('websales/{order}','Api\WebSaleController@select');
        Route::post('websales/{order}/status','Api\WebSaleController@status');
    });

    /** FILES */
    Route::get('files', 'Api\FileController@getFile');
    Route::post('files', 'Api\FileController@store');
    Route::get('files/apply', 'Api\FileController@apply');
});

/** PASSPORT **/
Route::group(['prefix' => 'auth'], function () {
    Route::post('login', 'Api\AuthController@login');
    Route::post('signup', 'Api\AuthController@signup');

    Route::group(['middleware' => 'auth:api'], function() {
        Route::get('logout', 'Api\AuthController@logout');
        Route::get('user', 'Api\UserController@select');
    });
});
