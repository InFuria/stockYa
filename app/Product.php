<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'slug', 'name', 'description', 'type', 'image', 'price', 'category_id', 'company_id', 'score', 'score_count', 'status'
    ];

    public function company(){
        return $this->belongsTo(Company::class, 'company_id');
    }
}
