<?php

namespace App\Http\Controllers\Api;

use App\Company;
use App\File;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Product;
use App\ProductCategory;
use App\Stock;
use App\User;
use App\WebSale;
use App\WebSaleDetail;
use App\WebSaleRecord;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;


class ProductController extends Controller
{

    public function index()
    {

        /*$cities = \DB::table('cities')->insert(
            ['name' => 'Goya']
        );

        $pay = \DB::table('payment_methods')->insert(
            ['name' => 'efectivo']
        );

        $units = \DB::table('units')->insert(
            ['name' => 'gr']
        );

        $user = new User();
        $user->dni = 12345678;
        $user->username = 'ely.admin';
        $user->name = 'Eliana Gimenez';
        $user->address = '00000';
        $user->phone = '00000';
        $user->status = 1;
        $user->email = 'eli_gimenez@outlook.com';
        $user->password = Hash::make('undertale');
        $user->save();

        $user->createToken('Personal Admin Token', ['*'])->accessToken;

        $user = new User();
        $user->dni = 123456789;
        $user->username = 'dany.admin';
        $user->name = 'Daniel Garcia';
        $user->address = '00000';
        $user->phone = '00000';
        $user->status = 1;
        $user->email = 'learfen001@gmail.com ';
        $user->password = Hash::make('undertale');
        $user->save();

        $user->createToken('Personal Admin Token', ['*'])->accessToken;*/

        factory(User::class, 50)->create();

        factory(Company::class, 5)->create();
        factory(ProductCategory::class, 50)->create();
        factory(Product::class, 50)->create();

        factory(WebSale::class, 20)->create();
        factory(WebSaleDetail::class, 15)->create();
        factory(WebSaleRecord::class, 15)->create();
    }

    public function getProducts()
    {
        try {

            if ($request = request()->get('company_id')) {

                $products = Product::with('stock', 'image:files.id,files.name')
                ->where('company_id', $request)->orderByDesc('id');

                if (is_integer($status = request()->get('status'))) {

                    $products = $products->where('status', request()->status)->paginate(50);
                } else {

                    $products = $products->paginate(50);
                }

                $company = Company::with('image:files.id,files.name')->where('id', $request)->first();

                return response()->json([
                    'company' => $company,
                    'products' => $products
                ]);
            }

            if ($request = request()->get('tag_id')) {

                $products = Product::with('company', 'image:files.id,files.name')
                    ->join('product_tag', 'product_tag.product_id', '=', 'products.id')
                    ->where('product_tag.tag_id', request()->tag_id)->orderByDesc('id');


                if (is_integer($status = request()->get('status'))) {

                    $products = $products->where('status', request()->status)->paginate(50);
                } else {

                    $products = $products->paginate(50);
                }

                return response()->json([
                    'products' => $products
                ]);
            }

            if (is_integer($status = request()->get('status'))) {

                $products = Product::where('status', (integer)request()->status)->with('company', 'tags', 'image:files.id,files.name')
                    ->orderByDesc('id')->paginate(50);
            } else {

                $products = Product::with('company', 'tags', 'image:files.id,files.name')->orderByDesc('id')->paginate(50);
            }

            return response()->json($products);

        } catch (\Exception $e) {
            Log::error('ProductController::getProducts - ' . $e->getMessage());
            return response()->json(['origin' => 'ProductController::getProducts', 'message' => $e->getMessage()], 400);
        }
    }

    public function store(ProductRequest $request)
    {
        DB::beginTransaction();
        try {

            $request = $request->all();
            $request['slug'] = urlencode($request['name']) . '+' . Carbon::now()->format('d_m_y');

            $product = Product::create($request);

            File::sync([], request()->image, $product, 'product');

            if (request()->get('tags'))
                $product->tags()->attach(request()->tags);

            DB::commit();

            $product->image =  Product::find($product->id)->image->map->only('id', 'name');
            $product->tags = $product->tags;

            return response()->json([
                'message' => 'El producto ha sido creado con exito!',
                'product' => $product->attributesToArray()], 201);

        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error('ProductController::store - ' . $qe->getMessage());
            return response()->json(['origin' => 'ProductController::store', 'message' => $qe->getMessage()], 400);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('ProductController::store - ' . $e->getMessage());
            return response()->json(['origin' => 'ProductController::store', 'message' => $e->getMessage()], 400);
        }
    }

    public function select(Product $product)
    {
        try {

            $product->sold = 0;
            $detail = WebSaleDetail::where('product_id', $product->id)->first();

            $sale = isset($detail) ? WebSale::find($detail->web_sale_id) : null;

            if ($detail != null && $sale->status == 1)
                $product->sold = WebSaleDetail::selectRaw("sum(quantity) as quantity")
                    ->join('web_sales', 'web_sales.id', '=', 'web_sale_detail.web_sale_id')
                    ->where('web_sales.status', 1)
                    ->where('product_id', $product->id)
                    ->groupByRaw("product_id")->orderBy('product_id')
                    ->first()->quantity;

            $category = DB::table('products_categories')->where('id', $product->category_id)->first();
            unset($product->category_id);
            $product->category = $category;

            $product->company_detail = $product->company;
            $product->tags = $product->tags;
            $product->stock = $product->stock == null ? 0 : $product->stock['quantity'];
            $product->image = $product->image->map->only('id', 'name');

            return response()->json($product->attributesToArray());

        } catch (\Exception $e) {
            Log::error('ProductController::select - ' . $e->getMessage());
            return response()->json(['origin' => 'ProductController::select', 'message' => $e->getMessage()], 400);
        }
    }

    public function update(Request $request, Product $product)
    {
        DB::beginTransaction();
        try {

            // temporal
            /*$request->validate([
                'name' => 'string',
                'description' => 'string',
                'type' => 'string',
                'price' => 'numeric',
                'category_id' => 'integer',
                'company_id' => 'integer',
                'status' => 'integer',
                'visits' => 'integer',
                'image' => 'array'
            ]);*/

            $request = $request->all();
            $request['slug'] = urlencode($request['name']) . '+' . Carbon::now()->format('d_m_y');

            $product->update($request);

            $old_files = $product->image->pluck('id')->toArray();
            File::sync($old_files, request()->image, $product, 'product');
            $product->image = Product::find($product->id)->image->map->only('id', 'name');

            if (request()->get('company_id'))
                $product->company()->update(['company_id' => request()->company_id]);

            if (request()->get('tags'))
                $product->tags()->sync(request()->tags);

            DB::commit();

            $product->company = $product->company;
            $product->tags = $product->tags;
            $product->stock = $product->stock == null ? 0 : $product->stock['quantity'];
            $product->image =  Product::find($product->id)->image->map->only('id', 'name');

            return response()->json([
                'message' => 'El producto se ha actualizado!',
                'product' => $product->attributesToArray()], 200);

        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error('ProductController::update - ' . $qe->getMessage());
            return response()->json(['origin' => 'ProductController::update', 'message' => $qe->getMessage()], 400);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('ProductController::update - ' . $e->getMessage());
            return response()->json(['origin' => 'ProductController::update', 'message' => $e->getMessage()], 400);
        }
    }

    public function destroy(Product $product)
    {
        DB::beginTransaction();
        try {

            $product->delete();
            $product->tags()->sync([]);
            $product->stock()->update([]);
            $product->files()->sync([]);
            DB::commit();

            return response('El producto ha sido eliminado', 200);

        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error('ProductController::destroy - ' . $qe->getMessage());
            return response()->json(['origin' => 'ProductController::destroy', 'message' => $qe->getMessage()], 400);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('ProductController::destroy - ' . $e->getMessage());
            return response()->json(['origin' => 'ProductController::destroy', 'message' => $e->getMessage()], 400);
        }
    }

    public function status(Product $product)
    {
        DB::beginTransaction();
        try {

            $product->status = $product->status == 1 ? 0 : 1;
            $product->save();
            DB::commit();

            return response('El estado del producto ha sido modificado', 200);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('ProductController::status - ' . $e->getMessage());
            return response()->json(['origin' => 'ProductController::status', 'message' => $e->getMessage()], 400);
        }
    }

    public function setScore(Product $product)
    {
        DB::beginTransaction();
        try {
            $old_score = $product->score * $product->score_count;
            $product->score = round(($old_score + request('calification')) / ($product->score_count + 1), 2);
            $product->score_count++;
            $product->save();
            DB::commit();

            return response()->json([
                'message' => 'Calificacion actualizada!',
                'product' => $product
            ], 200);

        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error('ProductController::setScore - ' . $qe->getMessage());
            return response()->json(['origin' => 'ProductController::setScore', 'message' => $qe->getMessage()], 400);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ProductController::setScore - ' . $e->getMessage());
            return response()->json(['origin' => 'ProductController::setScore', 'message' => $e->getMessage()], 400);
        }
    }

    public function setTags(Product $product)
    {
        DB::beginTransaction();
        try {

            $product->tags()->sync(request()->get('tags'));
            DB::commit();

            $tags = array_map(function ($e) {
                unset($e['pivot']);
                return $e;
            }, $product->tags->toArray());

            return response()->json([
                'message' => 'Etiquetas actualizadas!',
                'product' => $product->id,
                'tags' => $tags
            ], 200);

        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error('ProductController::setTags - ' . $qe->getMessage());
            return response()->json(['origin' => 'ProductController::setTags', 'message' => $qe->getMessage()], 400);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ProductController::setTags - ' . $e->getMessage());
            return response()->json(['origin' => 'ProductController::setTags', 'message' => $e->getMessage()], 400);
        }
    }

    public function visits(){}
}
