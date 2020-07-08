<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WebSale extends Model
{
    protected $table = 'web_sales';

    protected $fillable = ['company_id', 'client_id', 'address', 'payment_id', 'delivery', 'status', 'total', 'tracker', 'tags', 'text'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($query) {
            $query->status = 0; // 0 => pendiente, 1 => completado, 2 => cancelado, 3 => en revision
            $query->client_id = request()->user()->id;
            $query->tracker = Str::random();
            $query->total = 0;
        });
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function web_sale_details(){
        return $this->hasMany(WebSaleDetail::class);
    }

    public function company(){
        return $this->belongsTo(Company::class, 'company_id');
    }
}
