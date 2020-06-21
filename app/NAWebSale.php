<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NAWebSale extends Model
{
    protected $table = 'na_web_sales';

    protected $fillable = ['client_name', 'email', 'phone', 'company_id', 'status', 'total', 'tracker', 'tags', 'text'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($query) {
            $query->status = 0; // 0 => pendiente, 1 => completado, 2 => cancelado, 3 => en revision
            $query->tracker = Str::random();
        });
    }

    public function web_sale_details(){
        return $this->hasMany(NAWebSaleDetail::class, 'na_web_sale_id');
    }

    public function company(){
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function files(){
        return $this->belongsToMany(File::class, 'entities_files', 'entity_id', 'file_id')
            ->wherePivot('origin', 'nawebsale');
    }

    public function image(){
        return $this->files();
    }
}
