<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name', 'slug', 'address', 'email', 'phone', 'whatsapp', 'social', 'city_id', 'score', 'delivery', 'zone', 'status',
        'attention_hours','category_id', 'company_id', 'visits'
    ];
    protected $hidden = ['pivot'];
    public $timestamps= null;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($query) {
            $query->status = 1;
            $query->city_id = 1;
        });
    }

    public function setSlugAttribute($value){
        $this->attributes['slug'] = '+' . $value;
    }

    public function stock(){
        return $this->hasMany(Stock::class);
    }

    public function products(){
        return $this->belongsToMany(Product::class);
    }

    public function files(){
        return $this->belongsToMany(File::class, 'entities_files', 'entity_id', 'file_id')
            ->withPivot('origin')->wherePivot('origin','company');
    }

    public function image(){
        return $this->files();
    }
}
