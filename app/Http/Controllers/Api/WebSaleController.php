<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WebSaleRequest;
use App\Product;
use App\WebSale;
use App\WebSaleDetail;
use App\WebSaleRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WebSaleController extends Controller
{
    public function getOrders(){
        try {
            $user = request()->user();
            if ($user->isSeller())
                return response()->json(['data' => $user->company->web_sales]);

            if (request()->get('tracker'))
                return response()->json(['data' => $user->company->web_sales->where('tracker', request()->tracker)->first()]);

            $sales = WebSale::paginate(50);

            return response()->json(['data' => $sales],200);

        } catch (\Exception $e) {
            Log::error('WebSaleController::getOrders - ' . $e->getMessage());
            return response()->json(['origin' => 'WebSaleController:getOrders', 'message' => $e->getMessage()], 400);
        }
    }

    public function store(WebSaleRequest $request){
        try {
            DB::beginTransaction();

            $websale = WebSale::create($request->all());

            DB::commit();
            DB::beginTransaction();

            $sale_detail = request()->get('details');
            if (!isset($sale_detail))
                return response()->json(['message' => 'No se han seleccionado productos']);

            foreach ($sale_detail as $detail){
                $price = (Integer) Product::where('id', $detail['product_id'])->first()->price;

                $details = new WebSaleDetail();
                $details->web_sale_id = $websale->id;
                $details->product_id = $detail['product_id'];
                $details->quantity = $detail['quantity'];
                $details->subtotal = (Integer) $detail['quantity'] * $price;
                $details->saveOrFail();
            }

            $websale->total = array_sum($websale->web_sale_details->pluck('subtotal')->toArray());
            $websale->saveOrFail();

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

    public function update(Request $request, WebSale $order){
        try {
            DB::beginTransaction();

            $user = request()->user();
            if (!$user->isOwner($order) && !$user->isAdmin())
                return response()->json(['message' => 'No posee permisos para acceder a este registro'],403);

            $request = $request->validate([
                'company_id' => 'integer',
                'payment_id' => 'integer',
                'address' => 'string',
                'delivery' => 'boolean',
                'client_id' => 'integer',
                'status' => 'integer',
                'total' => 'numeric',
                'tags' => 'string',
                'text' => 'string'
            ]);
            $order->update($request);

            if ($sale_detail = request()->get('details')){
                $order->web_sale_details()->delete();
                foreach ($sale_detail as $detail){
                    $price = (Integer) Product::where('id', $detail['product_id'])->first()->price;

                    $details = new WebSaleDetail();
                    $details->web_sale_id = $order->id;
                    $details->product_id = $detail['product_id'];
                    $details->quantity = $detail['quantity'];
                    $details->subtotal = (Integer) $detail['quantity'] * $price;
                    $details->saveOrFail();
                }
            }

            $order->total = array_sum($order->web_sale_details->pluck('subtotal')->toArray());
            $order->saveOrFail();

            $record = new WebSaleRecord();
            $record->transaction_id = $order->id;
            $record->user_id = request()->user()->id; // se registra el usuario que gestiona la actualizacion
            $record->status = 1; // 0 => created, 1 => updated, 2 => deleted
            $record->saveOrFail();

            DB::commit();

            $order->details = $order->web_sale_details;

            return response()->json([
                'message' => 'El pedido se ha actualizado exitosamente!',
                'data' => $order->attributesToArray()
            ],200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('WebSaleController::update - ' . $e->getMessage());
            return response()->json(['origin' => 'WebSaleController:update', 'message' => $e->getMessage()], 400);
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
        try {
            DB::beginTransaction();

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

    public function sendTicket(WebSale $order){
        //
    }

    public function downloadTicket($tracker){
        try {
            $order = WebSale::where('tracker', $tracker)->first();

            return response()->download(storage_path() . '/tickets/' . $order->name . '');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('WebSaleController::downloadTicket - ' . $e->getMessage());
            return response()->json(['origin' => 'WebSaleController:downloadTicket', 'message' => $e->getMessage()], 400);
        }
    }
}
