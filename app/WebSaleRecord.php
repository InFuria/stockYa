<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WebTransactions extends Model
{
    protected $table = 'web_transactions';

    protected $fillable = ['transaction_id', 'user_id', 'status'];
}
