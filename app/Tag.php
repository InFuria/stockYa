<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    protected $fillable = ['name', 'type', 'status'];

    public $timestamps= null;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($query) {
            $query->status = 1;
        });
    }

    public function products(){
        return $this->belongsToMany(Product::class,'product_tag','tag_id', 'product_id');
    }
}
