<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\PdfToImage\Pdf as Pdf;

class NAWebSale extends Model
{
    protected $table = 'na_web_sales';

    protected $fillable = ['client_name', 'email', 'phone', 'company_id', 'status', 'total', 'tracker', 'tags', 'text'];

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

        $phone = $order->phone;
        $phone = str_replace(" ", "", $phone);
        $phone = str_replace("+", "", $phone);
        $phone = str_replace(".", "", $phone);

        $client_phone = $phone;
        $client_name = $order->client_name;
        $message = "Hola {$client_name}! Somos el equipo de PedidosGoya, estas recibiendo este mensaje porque se confirmo tu pedido en nuestra plataforma, te enviamos adjunto tu ticket con todos los detalles.

Si crees que hubo un error o necesitas soporte contactanos a futuroemail@gmail.com.

Muchas gracias por utilizar nuestro servicios!";


        $url_message = 'https://eu144.chat-api.com/instance141481/message?token=ukkt3cjnhraf0a70';
        $url_files = 'https://eu144.chat-api.com/instance141481/sendFile?token=ukkt3cjnhraf0a70';

        /** Send message */
        $json_message = json_encode([
            'phone' => $client_phone,
            'body' => $message
        ]);

        $options_message = stream_context_create(['http' => [
            'method'  => 'POST',
            'header'  => 'Content-type: application/json',
            'content' => $json_message
        ]]);


        /** Send ticket file */
        $filename = File::find($file)->name;
        $file_dir = storage_path(). '/tickets/' . $filename;
        $file = base64_encode(file_get_contents($file_dir));

        $pdf = new Pdf('http://stockya.local:92/api/files/' . File::find($file)->id);
        $pdf->saveImage(storage_path() . '/uploadedimages');

        echo $pdf;

        $json_ticket = json_encode([
            'phone' => $client_phone,
            'body' => 'data:image/jpg;base64,' . $file,
            'filename' => 'ticket_' . $filename
        ]);

        $options_ticket = stream_context_create(['http' => [
            'method'  => 'POST',
            'header'  => 'Content-type: application/json',
            'content' => $json_ticket
        ]]);

        //file_get_contents($url_message, false, $options_message);
        file_get_contents($url_files, false, $options_ticket);
    }
}
