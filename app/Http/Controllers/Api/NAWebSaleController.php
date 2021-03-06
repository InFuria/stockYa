<?php

namespace App\Http\Controllers\Api;

use App\Company;
use App\File;
use App\Http\Controllers\Controller;
use App\Http\Requests\NAWebSaleRequest;
use App\Mail\WebSaleConfirmationMail;
use App\NAWebSale;
use App\NAWebSaleDetail;
use App\NAWebSaleRecord;
use App\Product;
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
            foreach($sales as $key => $sale){
                $sale["products"] = $sale->web_sale_details()->with('detail')->get()->toArray();
            }
            return response()->json(['data' => $sales],200);

        } catch (\Exception $e) {
            Log::error('NAWebSaleController::getOrders - ' . $e->getMessage());
            return response()->json(['origin' => 'NAWebSaleController:getOrders', 'message' => $e->getMessage()], 400);
        }
    }

    public function pendingOrders($company_id = null){
        try {
            if (isset($company_id)){
                $sales['company'] = Company::whereId($company_id)->selectRaw("id, phone, whatsapp, name")->first()->toArray();
                $sales['orders'] = NAWebSale::pendingOrders()->where('company_id', $company_id)->get();

                return response()->json(['data' => $sales],200);
            }

            $sales = NAWebSale::pendingOrders()->with('company')->get();

            return response()->json(['data' => $sales],200);

        } catch (\Exception $e) {
            Log::error('NAWebSaleController::pendingOrders - ' . $e->getMessage());
            return response()->json(['origin' => 'NAWebSaleController:pendingOrders', 'message' => $e->getMessage()], 400);
        }
    }

    /** Funcion para listar los pedidos que ya fueron confirmados al cliente */
    public function dispatchedMessages($company_id = null){
        try {
            if (isset($company_id)){
                $sales['company'] = Company::whereId($company_id)->selectRaw("id, phone, whatsapp, name")->first()->toArray();
                $sales['orders'] = NAWebSale::dispatchedMessages()->where('company_id', $company_id)->get();

                return response()->json(['data' => $sales],200);
            }

            $sales = NAWebSale::dispatchedMessages()->with('company')->get();

            return response()->json(['data' => $sales],200);

        } catch (\Exception $e) {
            Log::error('NAWebSaleController::pendingOrders - ' . $e->getMessage());
            return response()->json(['origin' => 'NAWebSaleController:pendingOrders', 'message' => $e->getMessage()], 400);
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
                $price = (Integer) Product::where('id', $detail['product_id'])->first()->price;

                $details = new NAWebSaleDetail();
                $details->na_web_sale_id = $websale->id;
                $details->product_id = $detail['product_id'];
                $details->quantity = $detail['quantity'];
                $details->subtotal = (Integer) $detail['quantity'] * $price;
                $details->saveOrFail();
            }

            $delivery_cost = $request->delivery == true ? Company::find($websale->company_id)->delivery : 0 ;
            $websale->total = array_sum($websale->web_sale_details->pluck('subtotal')->toArray()) + $delivery_cost;
            $websale->saveOrFail();

            $record = new NAWebSaleRecord();
            $record->transaction_id = $websale->id;
            $record->user_id = null;
            $record->status = 0;
            $record->saveOrFail();

            DB::commit();

            $websale->details = $websale->web_sale_details;

            return response()->json([
                'message' => 'El pedido se ha registrado exitosamente!',
                'data' => $websale->attributesToArray()
            ],200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('NAWebSaleController::store - ' . $e->getMessage());
            return response()->json(['origin' => 'NAWebSaleController::store', 'message' => $e->getMessage()], 400);
        }
    }

    public function update(Request $request, NAWebSale $order){
        try {
            DB::beginTransaction();

            $request = $request->validate([
                'client_name' => 'string',
                'email' => 'string',
                'phone' => 'string',
                'address' => 'string',
                'delivery' => 'boolean',
                'company_id' => 'numeric',
                'tags' => 'string',
                'text' => 'string'
            ]);
            $order->update($request);

            if ($sale_detail = request()->get('details')){
                $order->web_sale_details()->delete();
                foreach ($sale_detail as $detail){
                    $price = (Integer) Product::where('id', $detail['product_id'])->first()->price;

                    $details = new NAWebSaleDetail();
                    $details->na_web_sale_id = $order->id;
                    $details->product_id = $detail['product_id'];
                    $details->quantity = $detail['quantity'];
                    $details->subtotal = (Integer) $detail['quantity'] * $price;
                    $details->saveOrFail();
                }
            }

            $delivery_cost = $order->delivery == true ? Company::find($order->company_id)->delivery : 0 ;
            $order->total = array_sum($order->web_sale_details->pluck('subtotal')->toArray()) + $delivery_cost;
            $order->saveOrFail();

            $record = new NAWebSaleRecord();
            $record->transaction_id = $order->id;
            $record->user_id = request()->user()->id;
            $record->status = 2;
            $record->saveOrFail();

            DB::commit();

            $order->details = $order->web_sale_details;

            return response()->json([
                'message' => 'El pedido se ha actualizado exitosamente!',
                'data' => $order->attributesToArray()
            ],200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('NAWebSaleController::update - ' . $e->getMessage());
            return response()->json(['origin' => 'NAWebSaleController:update', 'message' => $e->getMessage()], 400);
        }
    }

    public function select(NAWebSale $order){
        try {

            $order->details = $order->web_sale_details;

            return response()->json([
                'data' => $order->attributesToArray()],200);

        } catch (\Exception $e) {
            Log::error('NAWebSaleController::select - ' . $e->getMessage());
            return response()->json(['origin' => 'NAWebSaleController:select', 'message' => $e->getMessage()], 400);
        }
    }

    public function status(NAWebSale $order){
        try {
            DB::beginTransaction();

            //pendiente, confirmado, mensaje enviado, cancelado
            switch (request()->get('status')){
                case 0:
                    $order->status = 0;
                    break;
                case 1:
                    $order->status = 1;
                    break;
                case 2:
                    $order->status = 2;
                    break;
                case 3:
                    $order->status = 3;
                    break;
                default:
                    return response()->json(['message' => 'No se ha ingresado un estado valido.']);
            }
            $order->saveOrFail();
            DB::commit();

            $order->details = $order->web_sale_details;

            return response()->json([
                'message' => 'El estado de la orden ha sido modificado',
                'data' => $order->attributesToArray()],200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('NAWebSaleController::status - ' . $e->getMessage());
            return response()->json(['origin' => 'NAWebSaleController:status', 'message' => $e->getMessage()], 400);
        }
    }

    public function sendTicket(NAWebSale $order){
        try {

            $products = $order->web_sale_details()->with('detail')->get()->toArray();
            $delivery = $order->company->delivery;

            $pdf = PDF::loadView('emails.websaleconfirmation', [
                'banner_style' => "height: 50px; padding-bottom: 25px;",
                'order' => $order,
                'delivery' => $delivery,
                'products' => $products
            ]);

            $filename = "{$order->id}_" . Carbon::now()->format('Y_d_m_H_i_s') . ".pdf";
            $pdf->save(storage_path().'/tickets/' . $filename . '');

            if ($order->files->toArray() != []){
                $details = $order->files->first();
                $path = storage_path() . '/tickets/' . $details->name . '';

                $file = $details;
                $file->slug = "ticket+{$filename}";
                $file->name = $filename;
                $file->status = 1;
                $file->apply = 1;
                $file->saveOrFail();

                if (file_exists($path))
                    unlink($path);

                $file = $file->id;

            } else {
                $file = File::insertGetId([
                    'slug' => "ticket+{$filename}",
                    'name' => $filename,
                    'status' => 1,
                    'apply' => 1
                ]);
                File::sync([], [$file], $order, 'nawebsale');
            }

            if (isset($order->email))
                Mail::to("{$order->email}")->send(new WebSaleConfirmationMail($order, $file));

            /*if (isset($order->phone)){
                NAWebSale::sendTicketByWhatsapp($order, $file);
            }*/

            return response()->json([
                'message' => 'Se ha enviado el ticket al cliente!',
                'order' => $order
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('NAWebSaleController::sendTicket - ' . $e->getMessage());
            return response()->json(['origin' => 'NAWebSaleController:sendTicket', 'message' => $e->getMessage()], 400);
        }
    }

    public function downloadTicket($tracker){
        try {
            $order = NAWebSale::where('tracker', $tracker)->first();

            return response()->download(storage_path() . '/tickets/' . $order->name . '');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('NAWebSaleController::downloadTicket - ' . $e->getMessage());
            return response()->json(['origin' => 'NAWebSaleController:downloadTicket', 'message' => $e->getMessage()], 400);
        }
    }

    public function massiveStatus($status){
        try {
            $new = request()->get('new');
            $orders = request()->get('ids');

            if ($new < 0 || $new > 4)
                return response()->json(['message' => 'El estado ingresado para la asignacion no es valido'], 400);

            $order = NAWebSale::where('status', $status)->whereBetween('id', $orders)->update(['status' => $new]);

            return response()->json([
                'message' => 'El estado de las ordenes ha sido modificado',
                'count' => $order],200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('NAWebSaleController::massiveStatus - ' . $e->getMessage());
            return response()->json(['origin' => 'NAWebSaleController:massiveStatus', 'message' => $e->getMessage()], 400);
        }
    }
}
