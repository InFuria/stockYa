<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\File;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FileController extends Controller
{
    public function getFile() {
        try {
            if ($request = request()->file) {

                $file = File::where('name', $request)->first();
                return response()->json(["data"=>$file], 200);
            }
            return response()->json(["data"=>"Archivo no valida"], 200);
        } catch (\Exception $e){
            Log::error('FileController::store - ' . $e->getMessage());
            return response('Ha ocurrido un error.', 400)->json(['message' => $e->getMessage()]);
        }
    }

    public function store () {
        try{

            $imageName = time().'.'.request()->file->getClientOriginalExtension();
            $store = FileController::storeDB($imageName);
            request()->file->move(public_path('/uploadedimages'), $imageName);

            return response()->json($store);

        } catch (\Exception $e){
            Log::error('FileController::store - ' . $e->getMessage());
            return response('Ha ocurrido un error.', 400)->json(['message' => $e->getMessage()]);
        }
    }

    public function apply(){
        try{

            //

        } catch (\Exception $e){
            Log::error('FileController::apply - ' . $e->getMessage());
            return response('Ha ocurrido un error.', 400)->json(['message' => $e->getMessage()]);
        }
    }

    static public function storeDB($name)
    {
        DB::beginTransaction();
        try {
            $img = File::create([
                'slug' => urlencode($name),
                'name' => $name,
                'status' => 1,
                'apply' => 0
            ]);

            DB::commit();

            return $img;

        } catch (QueryException $qe){
            DB::rollBack();
            Log::error('FileController::storeDB - ' . $qe->getMessage());
            return response('Ha ocurrido un error al procesar la consulta', 400)->json(['message' => $qe->getMessage()]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('FileController::storeDB - ' . $e->getMessage());
            return response()->json(['error' => true, 'message' => $e->getMessage()], 404);
        }
    }

}
