<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WebSaleRecord extends Model
{
    protected $table = 'web_sale_records';

    protected $fillable = ['transaction_id', 'user_id', 'status'];
}
