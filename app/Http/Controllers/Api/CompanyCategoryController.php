<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CompanyCategoryController extends Controller
{
    public function getCategories(){
        try {

            $categories = DB::table('company_categories')->paginate(15);

            return response()->json($categories, 200);

        } catch (\Exception $e){
            Log::error('CompanyCategoryController::getCategories - ' . $e->getMessage());
            return response()->json(['origin' => 'CompanyCategoryController::getCategories', 'message' => $e->getMessage()], 400);
        }
    }

    public function select($category){
        try {

            $category = DB::table('company_categories')->where('id', $category)->get();

            return response()->json($category, 200);

        } catch (\Exception $e){
            Log::error('CompanyCategoryController::select - ' . $e->getMessage());
            return response()->json(['origin' => 'CompanyCategoryController::select', 'message' => $e->getMessage()], 400);
        }
    }

    public function store(){
        try {
            $request = request()->all();
            $category = DB::table('company_categories')->insert($request);

            return response()->json('Se han registrado los datos!', 200);

        } catch (\Exception $e){
            Log::error('CompanyCategoryController::store - ' . $e->getMessage());
            return response()->json(['origin' => 'CompanyCategoryController::store', 'message' => $e->getMessage()], 400);
        }
    }

    public function update($category){
        try {

            $category = DB::table('company_categories')->where('id', $category)->update(request()->all());

            return response()->json('Se han actualizado los datos!', 200);

        } catch (\Exception $e){
            Log::error('CompanyCategoryController::update - ' . $e->getMessage());
            return response()->json(['origin' => 'CompanyCategoryController::update', 'message' => $e->getMessage()], 400);
        }
    }

    public function destroy($category){
        try {

            DB::table('company_categories')->where('id', $category)->delete();

            return response()->json('La categoria ha sido eliminada!', 200);

        } catch (\Exception $e){
            Log::error('CompanyCategoryController::destroy - ' . $e->getMessage());
            return response()->json(['origin' => 'CompanyCategoryController::destroy', 'message' => $e->getMessage()], 400);
        }
    }
}
