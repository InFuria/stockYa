<?php

namespace App;


use Illuminate\Database\Eloquent\Model;
use App\Company;
use Illuminate\Support\Facades\DB;

class File extends Model
{
    public $timestamps = null;
    protected $table = 'files';
    protected $hidden = ['pivot'];
    protected $fillable = ['slug', 'name', 'status' , 'apply'];

    public function products(){
        return $this->belongsToMany(Product::class, 'entities_files')->getPivotColumns();
    }

    public function companies(){
        return $this->belongsToMany(Company::class);
    }

    public static function sync($old, $new, $entity, $origin){
        if (isset($new)){
            foreach ($new as $key){
                $data[$key] = ['origin' => "{$origin}"];
            }
            $entity->files()->sync($data);

            foreach ($new as $key) {
                $file = File::find($key);
                $file->apply = 1;
                $file->save();
            }
        }

        if (isset($old)){
            foreach ($old as $key){
                $assigned = DB::table('entities_files')->where('file_id', $key)->pluck('id');

                if (!isset($assigned)){
                $file = File::find($key);
                $file->apply = 0;
                $file->save();
                }
            }
        }
    }

}
