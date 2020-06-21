<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NAWebSaleRecord extends Model
{
    protected $table = 'na_web_sale_records';

    protected $fillable = ['transaction_id', 'user_id', 'status'];
}
