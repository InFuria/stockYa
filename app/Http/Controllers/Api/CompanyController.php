<?php

namespace App\Http\Controllers\Api;

use App\Company;
use App\File;
use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyRequest;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CompanyController extends Controller
{
    public function getCompanies(){
        try {

            $companies = Company::with('image:files.id,files.name')->orderByDesc('id');

            if (is_integer($status = request()->get('status'))){
                $companies = $companies->where('status', $status);
            }

            if ($category_id = request()->get('category_id')){
                $companies = $companies->where('category_id', $category_id)->where('status', 1);
            }

            // Por defecto se traen las empresas habilitadas
            $companies = isset($companies) ? $companies->paginate(50) : $companies->where('status', 1)->paginate(50);

            return response()->json($companies, 200);

        } catch (\Exception $e){
            Log::error('CompanyController::getCompanies - ' . $e->getMessage());
            return response()->json(['origin' => 'CompanyController::getCompanies', 'message' => $e->getMessage()], 400);
        }
    }

    public function select(Company $company)
    {
        try {

            $company->image = $company->image->map->only('id', 'name');

            $category = DB::table('company_categories')->where('id', $company->category_id)->first();
            unset($company->category_id);
            $company->category = $category;

            return response()->json($company->attributesToArray(), 200);

        } catch (\Exception $e) {
            Log::error('CompanyController::select - ' . $e->getMessage());
            return response()->json(['origin' => 'CompanyController::select', 'message' => $e->getMessage()], 400);
        }
    }

    public function store(CompanyRequest $request){
        DB::beginTransaction();
        try {

            $request = $request->all();
            $request['slug'] = urlencode($request['name']);

            $company = Company::create($request);

            File::sync([], request()->image, $company, 'company');
            DB::commit();

            $company->image =  Company::find($company->id)->image->map->only('id', 'name');

            return response()->json([
                'message' => 'La compañia ha sido registrada con exito!',
                'company' => $company->attributesToArray()], 201);

        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error('CompanyController::store - ' . $qe->getMessage());
            return response()->json(['request'=>$request,'origin' => 'CompanyController::store > db', 'message' => $qe->getMessage()], 400);
        } catch (\Exception $e){
            Log::error('CompanyController::store - ' . $e->getMessage());
            return response()->json(['request'=>$request,'origin' => 'CompanyController::store', 'message' => $e->getMessage()], 400);
        }
    }

    public function update(Request $request, Company $company){
        try {
            /*
            $request->validate([
                'id' => 'integer',
                'name' => 'string',
                'address' => 'string',
                'email' => 'string',
                'phone' => 'string',
                'whatsapp' => 'string',
                'social' => 'string',
                'city_id' => 'integer',
                'image' => 'array',
                'score' => 'integer',
                'delivery' => 'integer',
                'zone' => 'string',
                'status' => 'integer',
                'attention_hours' => 'string',
                'category_id' => 'integer',
                'company_id' => 'integer',
                'visits' => 'integer'
            ]);
            */
            $request = $request->all();
            $request['slug'] = urlencode($request['name']);

            $company->update($request);

            $old_files = $company->image->pluck('id')->toArray();
            File::sync($old_files, request()->image, $company, 'company');
            $company->image =  Company::find($company->id)->image->map->only('id', 'name');

            return response()->json([
                'message' => 'La compañia se ha actualizado!',
                'company' => $company->attributesToArray(),
                'all' => $request
            ], 200);

        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error('CompanyController::update - ' . $qe->getMessage());
            return response()->json(['origin' => 'CompanyController::update > db', 'message' => $qe->getMessage()], 400);
        } catch (\Exception $e){
            Log::error('CompanyController::update - ' . $e->getMessage());
            return response()->json([
                'origin' => 'CompanyController::update > invalid',
                'message' => $e->getMessage(),
                ], 400);
        }
    }

    public function destroy(Company $company){
        DB::beginTransaction();
        try {

            $company->delete();
            $company->stock()->update([]);
            $company->files()->sync([]);
            DB::commit();

            return response('La empresa ha sido eliminada', 200);

        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error('CompanyController::destroy - ' . $qe->getMessage());
            return response()->json(['origin' => 'CompanyController::destroy', 'message' => $qe->getMessage()], 400);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('CompanyController::destroy - ' . $e->getMessage());
            return response()->json(['origin' => 'CompanyController::destroy', 'message' => $e->getMessage()], 400);
        }
    }

    public function status(Company $company){
        DB::beginTransaction();
        try {

            $company->status = request()->get('status');
            $company->saveOrFail();
            DB::commit();

            return response()->json([
                'message' => 'El estado de la empresa ha sido modificado',
                'status' => $company->status],200);

        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error('CompanyController::status - ' . $qe->getMessage());
            return response()->json(['origin' => 'CompanyController::status', 'message' => $qe->getMessage()], 400);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('CompanyController::status - ' . $e->getMessage());
            return response()->json(['origin' => 'CompanyController:status', 'message' => $e->getMessage()], 400);
        }
    }

    public function visits(){}
}
