<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductCategoryController extends Controller
{
    public function getCategories(){
        try {

            $categories = DB::table('product_categories')->paginate(15);

            return response()->json($categories, 200);

        } catch (\Exception $e){
            Log::error('ProductCategoryController::getCategories - ' . $e->getMessage());
            return response()->json(['origin' => 'ProductCategoryController::getCategories', 'message' => $e->getMessage()], 400);
        }
    }

    public function select($category){
        try {

            $category = DB::table('product_categories')->where('id', $category)->get();

            return response()->json($category, 200);

        } catch (\Exception $e){
            Log::error('ProductCategoryController::select - ' . $e->getMessage());
            return response()->json(['origin' => 'ProductCategoryController::select', 'message' => $e->getMessage()], 400);
        }
    }

    public function store(){
        try {
            $request = request()->all();
            $category = DB::table('product_categories')->insert($request);

            return response()->json('Se han registrado los datos!', 200);

        } catch (\Exception $e){
            Log::error('ProductCategoryController::store - ' . $e->getMessage());
            return response()->json(['origin' => 'ProductCategoryController::store', 'message' => $e->getMessage()], 400);
        }
    }

    public function update($category){
        try {

            $category = DB::table('product_categories')->where('id', $category)->update(request()->all());

            return response()->json('Se han actualizado los datos!', 200);

        } catch (\Exception $e){
            Log::error('ProductCategoryController::update - ' . $e->getMessage());
            return response()->json(['origin' => 'ProductCategoryController::update', 'message' => $e->getMessage()], 400);
        }
    }

    public function destroy($category){
        try {

            DB::table('product_categories')->where('id', $category)->delete();

            return response()->json('La categoria ha sido eliminada!', 200);

        } catch (\Exception $e){
            Log::error('ProductCategoryController::destroy - ' . $e->getMessage());
            return response()->json(['origin' => 'ProductCategoryController::destroy', 'message' => $e->getMessage()], 400);
        }
    }
}
