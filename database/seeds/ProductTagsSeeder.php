<?php

use App\Tag;
use Illuminate\Database\Seeder;

class ProductTagsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tags = ['promo','online','series','higiene','medicamento controlado','comida rapida'];

        foreach ($tags as $value){
            $tag = new Tag();
            $tag->name = $value;
            $tag->save();
        }
    }
}
