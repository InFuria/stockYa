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
/*
Route::get('/', function (){
    return view('main.index');
});
*/
Route::get('/', 'ShareController@index');
Route::get('/share/{slug}', 'ShareController@share');

Route::get('/frontend', function (){
    return view('frontend');
});


Route::get('/components/{file}', function (Request $request){
    return response( file_get_contents( public_path('components/'.$request->file) ) )
        ->header('Content-Type', 'text/javascript');
});

Route::get('/js/{file}', function (Request $request){
    return response( file_get_contents( public_path('js/'.$request->file) ) )
        ->header('Content-Type', 'text/javascript');
});

Route::get('/assets/img/{file}', function (Request $request){
    $filename = public_path('assets/img/'.$request->file);
    $file_extension = strtolower(substr(strrchr($filename,"."),1));
    switch( $file_extension ) {
        case "gif": $ctype="image/gif"; break;
        case "png": $ctype="image/png"; break;
        case "jpeg":
        case "jpg": $ctype="image/jpeg"; break;
        case "svg": $ctype="image/svg+xml"; break;
        default:
    }

    return response( file_get_contents( $filename ) )
        ->header('Content-Type', $ctype);
});




function defineRoutesView($entity){
    Route::get("/$entity", function () use ($entity){
        return view($entity);
    });

    Route::get("/$entity/{folder}/{file}", function (Request $request) use ($entity){
        $type = 'css';
        $type = $request->folder == 'js' || $request->folder == 'components' ? 'javascript' : $type;
        return response( file_get_contents( public_path($entity.'/'.$request->folder."/".$request->file) ) )
            ->header('Content-Type', 'text/'.$type);
    });
}

defineRoutesView('admin');
defineRoutesView('orders');
