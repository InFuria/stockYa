<?php

namespace App\Http\Controllers\Api;

use App\File;
use App\Http\Controllers\Controller;
use App\Http\Requests\NAWebSaleRequest;
use App\Mail\WebSaleConfirmationMail;
use App\NAWebSale;
use App\NAWebSaleDetail;
use App\NAWebSaleRecord;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NAWebSaleController extends Controller
{
    public function getOrders(){
        try {
            $user = request()->user();
            if ($user->isSeller())
                return response()->json(['data' => $user->company->na_web_sales]);

            if (request()->get('tracker'))
                return response()->json(['data' => $user->company->na_web_sales->where('tracker', request()->tracker)->first()]);

            $sales = NAWebSale::paginate(50);

            return response()->json(['data' => $sales],200);

        } catch (\Exception $e) {
            Log::error('WebSaleController::getOrders - ' . $e->getMessage());
            return response()->json(['origin' => 'WebSaleController:getOrders', 'message' => $e->getMessage()], 400);
        }
    }

    public function store(NAWebSaleRequest $request){
        try {
            DB::beginTransaction();

            $websale = NAWebSale::create($request->all());

            DB::commit();
            DB::beginTransaction();

            $sale_detail = request()->get('details');
            if (!isset($sale_detail))
                return response()->json(['message' => 'No se han seleccionado productos']);

            foreach ($sale_detail as $detail){
                $details = new NAWebSaleDetail();
                $details->na_web_sale_id = $websale->id;
                $details->product_id = $detail['product_id'];
                $details->quantity = $detail['quantity'];
                $details->subtotal = $detail['subtotal'];
                $details->saveOrFail();
            }

            $record = new NAWebSaleRecord();
            $record->transaction_id = $websale->id;
            $record->user_id = null;
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

    public function update(Request $request, NAWebSale $websale){
        DB::beginTransaction();
        try {

            $request = $request->validate([
                'client_name' => 'string',
                'email' => 'string',
                'phone' => 'string',
                'company_id' => 'numeric',
                'total' => 'numeric',
                'tags' => 'string',
                'text' => 'string'
            ]);
            $websale->update($request);

            $websale->web_sale_details()->delete();
            if ($sale_detail = request()->get('details')){
                foreach ($sale_detail as $detail){
                    $details = new NAWebSaleDetail();
                    $details->na_web_sale_id = $websale->id;
                    $details->product_id = $detail['product_id'];
                    $details->quantity = $detail['quantity'];
                    $details->subtotal = $detail['subtotal'];
                    $details->save();
                }
            }

            $record = new NAWebSaleRecord();
            $record->transaction_id = $websale->id;
            $record->user_id = request()->user()->id; // se registra el usuario que gestiona la actualizacion
            $record->status = 1; // 0 => created, 1 => updated, 2 => deleted
            $record->save();

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

    public function select(NAWebSale $order){
        try {

            $order->details = $order->web_sale_details;

            return response()->json([
                'data' => $order->attributesToArray()],200);

        } catch (\Exception $e) {
            Log::error('WebSaleController::select - ' . $e->getMessage());
            return response()->json(['origin' => 'WebSaleController:select', 'message' => $e->getMessage()], 400);
        }
    }

    public function status(NAWebSale $order){
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

    public function sendTicket(NAWebSale $order){
        try {

            $products = $order->web_sale_details()->with('detail')->get()->toArray();
            $delivery = $order->company->delivery;

            $pdf = PDF::loadView('emails.websaleconfirmation', [
                'order' => $order,
                'delivery' => $delivery,
                'products' => $products]);

            $filename = "{$order->id}_" . Carbon::now()->format('Y_d_m_H_i_s') . ".pdf";
            $pdf->save(storage_path().'/tickets/' . $filename . '');

            $file = File::insertGetId([
                'slug' => "ticket+{$filename}",
                'name' => $filename,
                'status' => 1,
                'apply' => 1
            ]);
            File::sync([], [$file], $order, 'nawebsale');

            if (isset($order->email))
                Mail::to("federicolucena1994@gmail.com")->send(new WebSaleConfirmationMail($order));

            if (isset($order->phone)){
                NAWebSale::sendTicketByWhatsapp($order, $file);
            }

            return response()->json([
                'message' => 'Se ha enviado el ticket al cliente!',
                'order' => $order
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('WebSaleController::sendTicket - ' . $e->getMessage());
            return response()->json(['origin' => 'WebSaleController:sendTicket', 'message' => $e->getMessage()], 400);
        }
    }
}
