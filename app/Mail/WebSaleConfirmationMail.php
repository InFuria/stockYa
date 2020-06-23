<?php

namespace App\Mail;

use App\File;
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
    public $file;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(NAWebSale $order, $file)
    {
        $this->order = $order;
        $this->file = $file;
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

        return $this->subject('eCommerce - Detalle de Pedido')->view('emails.websaleconfirmation')
            ->with([
                'banner_style' => "",
                'delivery' => $delivery,
                'products' => $products])
            ->attach(storage_path(). '/tickets/' . File::find($this->file)->name, [
                'as' => "comprobante_orden_{$this->order->id}.pdf",
                'mime' => 'application/pdf',
            ]);
    }
}
