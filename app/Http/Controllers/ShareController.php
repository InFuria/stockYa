<?php

namespace App\Http\Controllers;

use App\Company;
use App\Product;
use Illuminate\Http\Request;

class ShareController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
    	$dominio = "https://pedidosgoya.com/";
        return view('share' , array(
        	"search"=>"Ofertas",
        	"type"=>"article",
        	"title"=>"PEDIDOS GOYA",
        	"description"=>"Encontra todo lo que necesitas en Goya",
        	"image"=>$dominio."/pedidosgoya.jpg",
        	"image_little"=>$dominio."/pedidosgoya.jpg",
        	"url"=>$dominio,
        	"site_name"=>"PEDIDOS GOYA",
        	"date_post"=>"2020-06-15T05:59:00+01:00",
        	"date_update"=>"2020-06-15T05:59:00+01:00",
        	"section"=>"index",
        	"tag"=>"goya,pedidos,delivery,comprar,vender",
        	"facebook_id"=>"",
        	"twitter_creator"=>"",
        	"site"=>$dominio
        ));
    }
    public function share()
    {
            if ($request = request()->slug){
                if(explode(' ', $request)[0] != "+"){
                    $res = Product::with('image:files.id,files.name')
                        ->where('status', '1')->whereRaw("slug='{$request}'")->get();
                }else{
                    $res = Company::with('image:files.id,files.name')
                        ->where('status', '1')
                        ->whereRaw("name like '%$request%'")->orderByDesc('id')->get();
                }
                #return response()->json($res);
                return view('share' , $res[0]);
            }
    }
}
