<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    protected $fillable = ['name', 'type'];

    public $timestamps= null;

    public function products(){
        return $this->belongsToMany(Product::class,'product_tag','tag_id', 'product_id');
    }
}
