<?php

namespace App\Http\Controllers\Api;

use App\Branch;
use App\Company;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Product;
use App\ProductCategory;
use App\User;
use App\WebSale;
use App\WebSaleDetail;
use App\WebSaleRecord;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{

    public function index(){

        factory(User::class, 50)->create();

        factory(Company::class, 50)->create();
        factory(ProductCategory::class, 50)->create();
        factory(Product::class, 50)->create();
        factory(Branch::class, 50)->create();

        factory(WebSale::class, 20)->create();
        factory(WebSaleDetail::class, 15)->create();
        factory(WebSaleRecord::class, 15)->create();

        $user = new User();
        $user->dni = 12345678;
        $user->username = 'mango';
        $user->name = 'Mango';
        $user->address = '00000';
        $user->phone = '00000';
        $user->status = 1;
        $user->email = 'admin@gmail.com';
        $user->password = 'undertale';
        $user->save();

        $user->createToken('Admin token');
    }

    public function getProducts()
    {
        try {

            if ($request = request()->get('company_id')) {

                $products = Product::where('company_id', $request);

                if (is_integer($status = request()->get('status'))) {

                    $products->where('status', request()->status)->with('tags')->get();
                } else{

                    $products->with('tags')->get();
                }

                $company = Company::where('id', $request)->first();

                return response()->json([
                    'products' => $products,
                    'company' => $company
                ]);
            }

            if ($request = request()->get('tag_id')) {

                $products = Product::with('company')
                    ->join('product_tag', 'product_tag.product_id', '=', 'products.id')
                    ->where('product_tag.product_id', request()->tag_id);

                if (is_integer($status = request()->get('status'))) {

                    $products->where('status', request()->status)->get();
                } else {

                    $products->get();
                }

                return response()->json([
                    'products' => $products
                ]);
            }

            if (is_integer($status = request()->get('status'))) {

                $products = Product::where('status', (Integer) request()->status)->with('company', 'tags')->paginate(15);
            } else {

                $products = Product::with('company', 'tags')->paginate(15);
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

            $product = Product::create([
                'slug' => $request->slug,
                'name' => $request->name,
                'description' => $request->description,
                'type' => $request->type,
                'image' => $request->image,
                'price' => $request->price,
                'category_id' => $request->category_id,
                'company_id' => $request->company_detail['id'],
                'score' => 0,
                'score_count' => 0,
                'status' => 1
            ]);

            if (request()->get('tags'))
                $product->tags()->attach(request()->tags);

            DB::commit();

            return response()->json([
                'message' => 'El producto ha sido creado',
                'product' => $product->tags], 201);

        } catch (QueryException $qe){
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
            $sale = WebSaleDetail::where('product_id', $product->id)->first();

            if ($sale != null)
            $product->sold = WebSaleDetail::selectRaw("sum(quantity) as quantity")
                ->join('web_sales', 'web_sales.id', '=', 'web_sale_detail.web_sale_id')
                ->where('web_sales.status', 1)
                ->where('product_id', $product->id)
                ->groupByRaw("product_id")->orderBy('product_id')
                ->first()->quantity;

            $product->company_detail = $product->company;
            $product->tags = $product->tags;

            return response()->json($product->attributesToArray());

        } catch (\Exception $e) {
            Log::error('ProductController::select - ' . $e->getMessage());
            return response()->json(['origin' => 'ProductController::select', 'message' => $e->getMessage()], 400);
        }
    }

    public function update(Product $product)
    {
        DB::beginTransaction();
        try {

            $request = request()->validate([
                'slug' => 'string',
                'name' => 'string',
                'description' => 'string',
                'type' => 'string',
                'image' => 'string',
                'price' => 'numeric',
                'category_id' => 'integer',
                'company_id' => 'integer',
                'status' => 'integer'
            ]);

            $product->update($request);

            if (request()->get('company_id'))
                $product->company()->associate(request()->company_id);

            if (request()->get('tags'))
                $product->tags()->sync(request()->tags);

            $product->save();
            DB::commit();

            return response()->json([
                'message' => 'El producto se ha actualizado!',
                'product' => $product->with('company', 'tags')->first()], 200);

        } catch (QueryException $qe){
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
            DB::commit();

            return response('El producto ha sido eliminado', 200);

        } catch (QueryException $qe){
            DB::rollBack();
            Log::error('ProductController::destroy - ' . $qe->getMessage());
            return response()->json(['origin' => 'ProductController::destroy', 'message' => $qe->getMessage()], 400);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('ProductController::destroy - ' . $e->getMessage());
            return response()->json(['origin' => 'ProductController::destroy', 'message' => $e->getMessage()], 400);
        }
    }

    public function status(Product $product){
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

    public function setScore(Product $product){
        DB::beginTransaction();
        try {
            $old_score = $product->score * $product->score_count;
            $product->score = round(($old_score + request('calification')) / ($product->score_count + 1), 2);
            $product->score_count ++;
            $product->save();
            DB::commit();

            return response()->json([
                'message' => 'Calificacion actualizada!',
                'product' => $product
                ], 200);

        } catch (QueryException $qe){
            DB::rollBack();
            Log::error('ProductController::setScore - ' . $qe->getMessage());
            return response()->json(['origin' => 'ProductController::setScore', 'message' => $qe->getMessage()], 400);
        } catch (\Exception $e){
            DB::rollBack();
            Log::error('ProductController::setScore - ' . $e->getMessage());
            return response()->json(['origin' => 'ProductController::setScore', 'message' => $e->getMessage()], 400);
        }
    }

    public function setTags(Product $product){
        DB::beginTransaction();
        try {

            $product->tags()->sync(request()->get('tags'));
            DB::commit();

            $tags = array_map(function($e) {
                unset($e['pivot']);
                return $e;
            }, $product->tags->toArray());

            return response()->json([
                'message' => 'Etiquetas actualizadas!',
                'product' => $product->id,
                'tags' => $tags
            ], 200);

        } catch (QueryException $qe){
            DB::rollBack();
            Log::error('ProductController::setTags - ' . $qe->getMessage());
            return response()->json(['origin' => 'ProductController::setTags', 'message' => $qe->getMessage()], 400);
        } catch (\Exception $e){
            DB::rollBack();
            Log::error('ProductController::setTags - ' . $e->getMessage());
            return response()->json(['origin' => 'ProductController::setTags', 'message' => $e->getMessage()], 400);
        }
    }
}
