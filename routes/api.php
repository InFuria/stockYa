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

// Ruta de carga de datos provisional xD
//Route::get('products/index', 'Api\ProductController@index');

/** PUBLIC ROUTES */
Route::get('products', 'Api\ProductController@getProducts');
Route::get('products/{product}', 'Api\ProductController@select');
Route::get('companies', 'Api\CompanyController@getCompanies');
Route::get('companies/{company}', 'Api\CompanyController@select');
Route::get('categories/companies', 'Api\CompanyCategoryController@getCategories');
Route::get('categories/companies/{category}', 'Api\CompanyCategoryController@select');
Route::get('categories/products', 'Api\ProductCategoryController@getCategories');
Route::get('categories/products/{category}', 'Api\ProductCategoryController@select');
Route::get('tags', 'Api\TagController@getTags');
Route::get('tags/{tag}', 'Api\TagController@select');
Route::post('nawebsales','Api\NAWebSaleController@store');
Route::get('nawebsales/{tracker}/download','Api\NAWebSaleController@downloadTicket')->name('download.ticket');
Route::get('nawebsales/pendingOrders','Api\NAWebSaleController@pendingOrders');

Route::post('products/{product}/visit', 'Api\ProductController@visits');
Route::post('companies/{company}/visit', 'Api\CompanyController@visits');


    /** ADMIN PANEL ROUTES  **/
    //Route::middleware(['can:isAdmin'])->group(function () {

        /** TAGS & CATEGORIES ROUTES */
        Route::resource('tags', 'Api\TagController')->except(['index', 'create', 'edit', 'show']);
        Route::post('tags/{tag}/status', 'Api\TagController@status');

        Route::post('categories/companies', 'Api\CompanyCategoryController@store');
        Route::put('categories/companies/{category}', 'Api\CompanyCategoryController@update');
        Route::patch('categories/companies/{category}', 'Api\CompanyCategoryController@update');
        Route::delete('categories/companies/{category}', 'Api\CompanyCategoryController@destroy');

        Route::post('categories/products', 'Api\ProductCategoryController@store');
        Route::put('categories/products/{product}', 'Api\ProductCategoryController@update');
        Route::patch('categories/products/{product}', 'Api\ProductCategoryController@update');
        Route::delete('categories/products/{product}', 'Api\ProductCategoryController@destroy');


        /** FILES */
        Route::get('files/{file}', 'Api\FileController@getFile');
        Route::post('files', 'Api\FileController@store');
        Route::get('files/apply', 'Api\FileController@apply');


        /** COMPANIES ROUTES **/
        Route::post('companies', 'Api\CompanyController@store');
        Route::put('companies/{company}', 'Api\CompanyController@update');
        Route::patch('companies/{company}', 'Api\CompanyController@update');
        Route::delete('companies/{company}', 'Api\CompanyController@destroy');
        Route::post('companies/{company}/status', 'Api\CompanyController@status');

        Route::post('products', 'Api\ProductController@store');
        Route::put('products/{product}', 'Api\ProductController@update');
        Route::patch('products/{product}', 'Api\ProductController@update');
        Route::delete('products/{product}', 'Api\ProductController@destroy');
        Route::post('products/{product}/status', 'Api\ProductController@status');
        Route::post('products/{product}/setTags', 'Api\ProductController@setTags');


    Route::post('products/{product}/setScore', 'Api\ProductController@setScore');


Route::middleware(['auth:api'])->group(function () {

    Route::post('users','Api\UserController@store');
    Route::put('users/{user}','Api\UserController@update');
    Route::patch('users/{user}','Api\UserController@update');
    Route::delete('users/{user}','Api\UserController@destroy');
    Route::post('users/{user}/ban','Api\UserController@ban');

    Route::get('roles', 'Api\RoleController@getRoles');
    Route::post('roles','Api\RoleController@store');
    Route::get('roles/{role}', 'Api\RoleController@select');
    Route::put('roles/{role}','Api\RoleController@update');
    Route::patch('roles/{role}','Api\RoleController@update');
    Route::delete('roles/{role}','Api\RoleController@destroy');
    Route::post('roles/{role}/ban','Api\RoleController@ban');


    Route::middleware(['check.access'])->group(function () {

        /** WEB SALES ROUTES */
        Route::get('websales','Api\WebSaleController@getOrders');
        Route::post('websales','Api\WebSaleController@store');
        Route::put('websales/{order}','Api\WebSaleController@update');
        Route::patch('websales/{order}','Api\WebSaleController@update');
        Route::get('websales/{order}','Api\WebSaleController@select');
        Route::post('websales/{order}/status','Api\WebSaleController@status');


        /** NO AUTH WEB SALES ROUTES */
        Route::get('nawebsales','Api\NAWebSaleController@getOrders');
        Route::put('nawebsales/{order}','Api\NAWebSaleController@update');
        Route::patch('nawebsales/{order}','Api\NAWebSaleController@update');
        Route::get('nawebsales/{order}','Api\NAWebSaleController@select');
        Route::post('nawebsales/{order}/status','Api\NAWebSaleController@status');
        Route::get('nawebsales/{order}/sendTicket','Api\NAWebSaleController@sendTicket');
    });
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
