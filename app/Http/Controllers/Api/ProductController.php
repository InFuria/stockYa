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
    }

    public function getProducts()
    {
        try {
            if ($request = request()->get('company_id')) {

                $products = Product::where('company_id', $request)->where('status', 1)->paginate(15);
                $company = Company::where('id', $request)->first();

                return response()->json([
                    'products' => $products,
                    'company' => $company
                ]);
            }

            $products = Product::with('company')->paginate(15);

            return response()->json($products);

        } catch (\Exception $e) {
            Log::error('ProductController::getProducts - ' . $e->getMessage());
            return response('Ha ocurrido un error.', 400)->json(['message' => $e->getMessage()]);
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

            DB::commit();

            return response()->json([
                'message' => 'El producto ha sido creado',
                'product' => $product], 201);

        } catch (QueryException $qe){
            DB::rollBack();
            Log::error('ProductController::store - ' . $qe->getMessage());
            return response('Ha ocurrido un error al procesar la consulta', 400)->json(['message' => $qe->getMessage()]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('ProductController::store - ' . $e->getMessage());
            return response()->json(['error' => true, 'message' => $e->getMessage()], 404);
        }
    }

    public function select(Product $product)
    {
        try {
            $product->sold = WebSaleDetail::selectRaw("sum(quantity) as quantity")
                ->join('web_sales', 'web_sales.id', '=', 'web_sale_detail.web_sale_id')
                ->where('web_sales.status', 1)
                ->where('product_id', $product->id)
                ->groupByRaw("product_id")->orderBy('product_id')
                ->first()->quantity;

            $product->company_detail = $product->company;

            return response()->json($product->attributesToArray());

        } catch (\Exception $e) {
            Log::error('ProductController::select - ' . $e->getMessage());
            return response('Ha ocurrido un error al buscar el producto seleccionado.', 400)->json(['message' => $e->getMessage()]);
        }
    }

    public function update(ProductRequest $request, Product $product)
    {
        DB::beginTransaction();
        try {

            $product->update($request->all());
            $product->company()->associate($request->company_detail['id']);
            $product->save();
            DB::commit();

            return response([
                'message' => 'El producto se ha actualizado!',
                'product' => $product], 200);

        } catch (QueryException $qe){
            DB::rollBack();
            Log::error('ProductController::update - ' . $qe->getMessage());
            return response('Ha ocurrido un error al procesar la consulta', 400)->json(['message' => $qe->getMessage()]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('ProductController::update - ' . $e->getMessage());
            return response('Ha ocurrido un error al actualizar el producto', 400)->json(['message' => $e->getMessage()]);
        }
    }

    public function destroy(Product $product)
    {
        DB::beginTransaction();
        try {

            $product->delete();
            DB::commit();

            return response('El producto ha sido eliminado', 200);

        } catch (QueryException $qe){
            DB::rollBack();
            Log::error('ProductController::destroy - ' . $qe->getMessage());
            return response('Ha ocurrido un error al procesar la consulta', 400)->json(['message' => $qe->getMessage()]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('ProductController::destroy - ' . $e->getMessage());
            return response('Ha ocurrido un error al eliminar un producto', 400)->json(['message' => $e->getMessage()]);
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
            return response('Ha ocurrido un error al modificar el estado del producto', 400)->json(['message' => $e->getMessage()]);
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
            return response('Ha ocurrido un error al procesar la consulta', 400)->json(['message' => $qe->getMessage()]);
        } catch (\Exception $e){
            DB::rollBack();
            Log::error('ProductController::setScore - ' . $e->getMessage());
            return response('Ha ocurrido un error al procesar la peticion', 400)->json(['message' => $e->getMessage()]);
        }
    }
}
