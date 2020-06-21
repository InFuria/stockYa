<?php

namespace App\Mail;

use App\NAWebSale;
use App\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WebSaleConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(NAWebSale $order)
    {
        $this->order = $order;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $products = $this->order->web_sale_details()->with('detail')->get()->toArray();
        $delivery = $this->order->company->delivery;

        return $this->subject('PedidosGoya eCommerce - Detalle de Pedido')->view('emails.websaleconfirmation')
            ->with([
                'delivery' => $delivery,
                'products' => $products]);
    }
}
