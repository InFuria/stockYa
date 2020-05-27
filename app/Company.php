<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name', 'email', 'score', 'delivery', 'status'
    ];

    public $timestamps= null;

    public function products(){
        return $this->hasMany(Product::class);
    }
}
