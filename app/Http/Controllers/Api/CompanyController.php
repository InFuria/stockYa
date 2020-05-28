<?php

namespace App\Http\Controllers\Api;

use App\Company;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CompanyController extends Controller
{
    public function getCompanies(){
        try {

            if ($status = request()->get('status')){

                $companies = Company::where('status', $status)->get();
            }

            if ($category_id = request()->get('category_id')){

                $companies = Company::where('category_id', $category_id)->where('status', 1)->get()->toArray();
            }

            if (! request()->input())
                $companies = Company::where('status', 1)->get()->toArray();

            return response()->json($companies, 200);

        } catch (\Exception $e){
            Log::error('CompanyController::getCompanies - ' . $e->getMessage());
            return response('Ha ocurrido un error.', 400)->json(['message' => $e->getMessage()]);
        }
    }

    public function listCategories(){
        try {

            $categories = DB::table('company_categories')->get();

            return response()->json($categories, 200);

        } catch (\Exception $e){
            Log::error('CompanyController::listCategories - ' . $e->getMessage());
            return response('Ha ocurrido un error.', 400)->json(['message' => $e->getMessage()]);
        }
    }
}
