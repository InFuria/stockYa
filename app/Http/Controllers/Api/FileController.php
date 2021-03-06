<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\File;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FileController extends Controller
{
    public function getFile($file) {
        try {
            if (isset($file)) {

                $file = File::where('id', $file)->first();
                if (strpos($file->name, '.pdf'))
                    return response()->file(storage_path('/tickets/') . $file->name);

                return response()->file(public_path('/uploadedimages/') . $file->name);
            }

            return response()->json(["message"=>"Archivo no encontrado"], 200);

        } catch (\Exception $e){
            Log::error('FileController::store - ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 400);
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
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function apply(){
        try{

            //

        } catch (\Exception $e){
            Log::error('FileController::apply - ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 400);
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
            return response()->json(['message' => $qe->getMessage()], 400);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('FileController::storeDB - ' . $e->getMessage());
            return response()->json(['error' => true, 'message' => $e->getMessage()], 400);
        }
    }

}
