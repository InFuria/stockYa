<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    public $timestamps = null;
    protected $table = 'files';
    protected $hidden = ['pivot'];
    protected $fillable = ['slug', 'name', 'status' , 'apply'];

    public function product(){
        return $this->belongsToMany(Product::class, 'entities_files');
    }

    public function company(){
        return $this->belongsToMany(Company::class);
    }
}
