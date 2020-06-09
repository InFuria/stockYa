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

Auth::routes();

Route::get('/', function (){
    return view('main.index');
});

Route::get('/frontend', function (){
    return view('frontend');
});

Route::get('/admin', function (){
    return view('admin');
});

Route::get('/admin/{folder}/{file}', function (Request $request){
    $type = 'css';
    $type = $request->folder == 'js' || $request->folder == 'components' ? 'javascript' : $type;
    return response( file_get_contents( public_path('admin/'.$request->folder."/".$request->file) ) )
            ->header('Content-Type', 'text/'.$type);
});
