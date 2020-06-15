<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WebSaleRequest;
use App\WebSale;
use App\WebSaleDetail;
use App\WebSaleRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            $record->status = 0;// 0 => created, 1 => updated, 2 => deleted
            $record->saveOrFail();

            DB::commit();

            $websale->details = $websale->web_sale_details;

            return response()->json([
                'message' => 'El pedido se ha registrado exitosamente!',
                'data' => $websale->attributesToArray()
            ],200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('WebSaleController::store - ' . $e->getMessage());
            return response()->json(['origin' => 'WebSaleController::store', 'message' => $e->getMessage()], 400);
        }
    }

    public function update(Request $request, WebSale $websale){
        DB::beginTransaction();
        try {

            $user = request()->user();
            if (!$user->isOwner($websale) && !$user->isAdmin())
                return response()->json(['message' => 'No posee permisos para acceder a este registro'],403);

            $request = $request->validate([
                'company_id' => 'integer',
                'payment_id' => 'integer',
                'client_id' => 'integer',
                'status' => 'integer',
                'total' => 'numeric',
                'tags' => 'string',
                'text' => 'string'
            ]);
            $websale->update([$request]);

            if ($sale_detail = request()->get('details')){
                $websale->web_sale_details()->delete();
                foreach ($sale_detail as $detail){
                    $details = new WebSaleDetail();
                    $details->web_sale_id = $websale->id;
                    $details->product_id = $detail['product_id'];
                    $details->quantity = $detail['quantity'];
                    $details->total = $detail['total'];
                    $details->saveOrFail();
                }
            }

            $record = new WebSaleRecord();
            $record->transaction_id = $websale->id;
            $record->user_id = request()->user()->id; // se registra el usuario que gestiona la actualizacion
            $record->status = 1; // 0 => created, 1 => updated, 2 => deleted
            $record->saveOrFail();

            DB::commit();

            $websale->details = $websale->web_sale_details;

            return response()->json([
                'message' => 'El pedido se ha actualizado exitosamente!',
                'data' => $websale->attributesToArray()
            ],200);

        } catch (\Exception $e) {
            DB::rollBack();
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

            $user = request()->user();
            if (!$user->isOwner($order) && !$user->isAdmin())
                return response()->json(['message' => 'No posee permisos para acceder a este registro'],403);

            $order->details = $order->web_sale_details;

            return response()->json([
                'data' => $order->attributesToArray()],200);

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

            $order->details = $order->web_sale_details;

            return response()->json([
                'message' => 'El estado de la orden ha sido modificado',
                'data' => $order->attributesToArray()],200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('WebSaleController::status - ' . $e->getMessage());
            return response()->json(['origin' => 'WebSaleController:status', 'message' => $e->getMessage()], 400);
        }
    }
}
