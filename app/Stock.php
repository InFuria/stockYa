<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Stock extends Pivot
{
    protected $table = 'stock';

    public $incrementing = true;

    protected $fillable = ['product_id', 'company_id', 'quantity', 'unit_id'];

    public function company(){
        return $this->belongsTo(Company::class);
    }

    public function product(){
        return $this->belongsTo(Product::class);
    }
}
