<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WebSaleDetail extends Model
{
    protected $table = 'web_sale_detail';

    public $timestamps = null;

    protected $fillable = ['web_sale_id', 'product_id', 'quantity', 'total'];
}
