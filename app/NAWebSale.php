<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
//use Spatie\PdfToImage\Pdf as Pdf;

class NAWebSale extends Model
{
    protected $table = 'na_web_sales';

    protected $fillable = ['client_name', 'email', 'phone', 'address', 'company_id', 'delivery', 'status', 'total', 'tracker', 'tags', 'text'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($query) {
            $query->status = 0; // 0 => pendiente, 1 => completado, 2 => cancelado, 3 => en revision
            $query->tracker = Str::random();
            $query->total = 0;
        });
    }

    public function web_sale_details(){
        return $this->hasMany(NAWebSaleDetail::class, 'na_web_sale_id');
    }

    public function company(){
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function files(){
        return $this->belongsToMany(File::class, 'entities_files', 'entity_id', 'file_id')
            ->wherePivot('origin', 'nawebsale');
    }

    public function image(){
        return $this->files();
    }

    public static function sendTicketByWhatsapp(NAWebSale $order, $file){

        $url = 'https://eu144.chat-api.com/instance141481/message?token=ukkt3cjnhraf0a70';
        $link = route('download.ticket', ['tracker' => $order->tracker]);

        $phone = $order->phone;
        $phone = str_replace(" ", "", $phone);
        $phone = str_replace("+", "", $phone);
        $phone = str_replace(".", "", $phone);

        $client_phone = $phone;
        $client_name = $order->client_name;
        $message = "Hola {$client_name}! Somos el equipo de PedidosGoya, estas recibiendo este mensaje porque se confirmo tu pedido en nuestra plataforma, te enviamos adjunto tu ticket con todos los detalles.

Si crees que hubo un error o necesitas soporte contactanos a futuroemail@gmail.com.

Para descargar tu comprobante de compra ingresa al siguiente link: {$link}

Muchas gracias por utilizar nuestro servicios!";


        /** Send message */
        $json = json_encode([
            'phone' => $client_phone,
            'body' => $message
        ]);

        $options = stream_context_create(['http' => [
            'method'  => 'POST',
            'header'  => 'Content-type: application/json',
            'content' => $json
        ]]);

        file_get_contents($url, false, $options);
    }
}
