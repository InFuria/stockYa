<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    protected $fillable = [
        'slug', 'name', 'description', 'type', 'image', 'price', 'category_id', 'company_id', 'score', 'score_count', 'status'
    ];

    public function company(){
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function tags(){
        return $this->belongsToMany(Tag::class, 'product_tag', 'product_id', 'tag_id');
    }
}
