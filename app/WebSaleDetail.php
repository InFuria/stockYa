<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WebSaleDetail extends Model
{
    public $timestamps = null;

    protected $table = 'web_sale_detail';

    protected $fillable = ['web_sale_id', 'product_id', 'quantity', 'total'];

    public function web_sale(){
        return $this->belongsTo(WebSale::class);
    }

    public function detail(){
        return $this->belongsTo(Product::class, 'product_id');
    }
}
