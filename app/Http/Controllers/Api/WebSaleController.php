<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use App\WebSale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Client;

class WebSaleController extends Controller
{
    public function register(){
        DB::beginTransaction();
        try {



            return response()->json('',200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('WebSaleController::register - ' . $e->getMessage());
            return response('Ha ocurrido un error.', 400)->json(['message' => $e->getMessage()]);
        }
    }

    /** TESTED | NEED MIDDLEWARE */
    public function tracking(){
        try {

            $user = User::findOrFail(request()->user()->id);

            return response()->json($user->websales()->orderBy('status', 'DESC')->paginate(10),200);

        } catch (\Exception $e) {
            Log::error('WebSaleController::tracking - ' . $e->getMessage());
            return response('Ha ocurrido un error.', 400)->json(['message' => $e->getMessage()]);
        }
    }

    /** TESTED */
    public function selectOrder(WebSale $order){
        try {

            return response()->json($order,200);

        } catch (\Exception $e) {
            Log::error('WebSaleController::selectOrder - ' . $e->getMessage());
            return response('Ha ocurrido un error.', 400)->json(['message' => $e->getMessage()]);
        }
    }

    /** TESTED */
    public function changeStatus(WebSale $order){
        DB::beginTransaction();
        try {

            $order->status = request()->get('status');
            $order->save();
            DB::commit();

            return response()->json('El estado de la orden ha sido modificado',200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('WebSaleController::changeStatus - ' . $e->getMessage());
            return response('Ha ocurrido un error.', 400)->json(['message' => $e->getMessage()]);
        }
    }

}
