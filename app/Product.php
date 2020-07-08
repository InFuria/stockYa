<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    protected $fillable = [
        'slug', 'name', 'description', 'type', 'price', 'category_id', 'company_id', 'score', 'score_count', 'status', 'visits'
    ];

    protected $hidden = ['pivot'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($query) {
            $query->score = 0;
            $query->score_count = 0;
            $query->status = 1;
            $query->visits = 0;
        });
    }

    public function stock(){
        return $this->hasOne(Stock::class);
    }

    public function company(){
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function tags(){
        return $this->belongsToMany(Tag::class, 'product_tag', 'product_id', 'tag_id');
    }

    public function files(){
        return $this->belongsToMany(File::class, 'entities_files', 'entity_id', 'file_id')
            ->wherePivot('origin', 'product');
    }

    public function image(){
        return $this->files();
    }

    public function web_sale(){
        return $this->belongsToMany(WebSaleDetail::class);
    }

    public function na_web_sale(){
        return $this->belongsToMany(NAWebSaleDetail::class);
    }
}
