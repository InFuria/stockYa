<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WebSaleRequest;
use App\WebSale;
use App\WebSaleDetail;
use App\WebSaleRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WebSaleController extends Controller
{

    public function store(WebSaleRequest $request){
        DB::beginTransaction();
        try {

            $websale = WebSale::create($request->all());

            DB::commit();
            DB::beginTransaction();

            $sale_detail = request()->get('details');
            foreach ($sale_detail as $detail){
                $details = new WebSaleDetail();
                $details->web_sale_id = $websale->id;
                $details->product_id = $detail['product_id'];
                $details->quantity = $detail['quantity'];
                $details->total = $detail['total'];
                $details->saveOrFail();
            }

            $record = new WebSaleRecord();
            $record->transaction_id = $websale->id;
            $record->user_id = $websale->client_id;
            $record->status = 0;
            $record->saveOrFail();

            DB::commit();

            $websale->details = $websale->web_sale_details;

            return response()->json([
                'message' => 'El pedido se ha registrado exitosamente!',
                'order' => $websale->attributesToArray()
            ],200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('WebSaleController::store - ' . $e->getMessage());
            return response('Ha ocurrido un error.', 400)->json(['message' => $e->getMessage()]);
        }
    }

    public function update(){
        try {

            //

        } catch (\Exception $e) {
            Log::error('WebSaleController::update - ' . $e->getMessage());
            return response()->json(['origin' => 'WebSaleController:update', 'message' => $e->getMessage()], 400);
        }
    }

    public function tracking(){
        try {

            $sales = request()->user()->websales()->with('web_sale_details')->orderBy('status', 'DESC');

            if (request()->get('tracker'))
                $sales = $sales->where('tracker', request()->tracker);

            $sales = $sales->paginate(25);

            return response()->json($sales,200);

        } catch (\Exception $e) {
            Log::error('WebSaleController::tracking - ' . $e->getMessage());
            return response()->json(['origin' => 'WebSaleController:tracking', 'message' => $e->getMessage()], 400);
        }
    }

    public function select(WebSale $order){
        try {

            $order->details = $order->web_sale_details;

            return response()->json($order->attributesToArray(),200);

        } catch (\Exception $e) {
            Log::error('WebSaleController::select - ' . $e->getMessage());
            return response()->json(['origin' => 'WebSaleController:select', 'message' => $e->getMessage()], 400);
        }
    }

    public function status(WebSale $order){
        DB::beginTransaction();
        try {

            $order->status = request()->get('status');
            $order->saveOrFail();
            DB::commit();

            return response()->json([
                'message' => 'El estado de la orden ha sido modificado',
                'status' => $order->status],200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('WebSaleController::status - ' . $e->getMessage());
            return response()->json(['origin' => 'WebSaleController:status', 'message' => $e->getMessage()], 400);
        }
    }

}
