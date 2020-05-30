<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WebSale extends Model
{
    protected $table = 'web_sales';

    protected $fillable = ['branch_id', 'client_id', 'payment_id', 'status'];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
