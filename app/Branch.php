<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = ['code', 'city_id', 'company_id', 'name', 'address', 'phone'];
}
