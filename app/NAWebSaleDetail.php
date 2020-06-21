<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NAWebSaleDetail extends Model
{
    public $timestamps = null;

    protected $table = 'na_web_sale_detail';

    protected $fillable = ['na_web_sale_id', 'product_id', 'quantity', 'subtotal'];

    public function detail(){
        return $this->belongsTo(Product::class, 'product_id');
    }
}
