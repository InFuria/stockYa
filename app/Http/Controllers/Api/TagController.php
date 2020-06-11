<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TagController extends Controller
{
    public function getTags(){
        try {

            $tags = Tag::all();

            if (request()->get('status'))
                $tags = Tag::where('status', request()->status)->get();

            return response()->json($tags, 200);

        } catch (\Exception $e){
            Log::error('TagController::getTags - ' . $e->getMessage());
            return response()->json(['origin' => 'TagController::getTags', 'message' => $e->getMessage()], 400);
        }
    }

    public function select(Tag $tag)
    {
        try {

            return response()->json($tag, 200);

        } catch (\Exception $e){
            Log::error('TagController::select - ' . $e->getMessage());
            return response()->json(['origin' => 'TagController::select', 'message' => $e->getMessage()], 400);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {

            $request = $request->validate([
                'name'     => 'required|string',
                'type'     => 'integer'
            ]);

            $tag = Tag::create($request);
            DB::commit();

            return response()->json([
                'message' => 'Se ha creado la etiqueta!',
                'tag' => $tag
            ], 200);

        } catch (\Exception $e){
            DB::rollBack();
            Log::error('TagController:: - ' . $e->getMessage());
            return response()->json(['origin' => 'TagController::store', 'message' => $e->getMessage()], 400);
        }
    }

    public function update(Request $request, Tag $tag)
    {
        DB::beginTransaction();
        try {
            $request = $request->validate([
                'name'     => 'string',
                'type'     => 'integer',
                'status'   => 'integer'
            ]);

            $tag->update($request);
            $tag->saveOrFail();
            DB::commit();

            return response()->json([
                'message' => 'Se ha actualizado la etiqueta!',
                'tag' => $tag
            ],200);

        } catch (\Exception $e){
            DB::rollBack();
            Log::error('TagController::update - ' . $e->getMessage());
            return response()->json(['origin' => 'TagController::update', 'message' => $e->getMessage()], 400);
        }
    }

    public function destroy(Tag $tag)
    {
        DB::beginTransaction();
        try {
            $tag->delete();
            DB::commit();

            return response()->json([
                'message' => 'La etiqueta ha sido eliminada!'
            ], 200);
        } catch (\Exception $e){
            DB::rollBack();
            Log::error('TagController::destroy - ' . $e->getMessage());
            return response()->json(['origin' => 'TagController::destroy', 'message' => $e->getMessage()], 400);
        }
    }

    public function status(Tag $tag){
        DB::beginTransaction();
        try {

            $tag->status = $tag->status == 1 ? 0 : 1;
            $tag->save();
            DB::commit();

            return response()->json([
                'message' => 'El estado de la etiqueta ha sido cambiado!',
                'tag' => $tag
            ], 200);

        } catch (\Exception $e){
            DB::rollBack();
            Log::error('TagController::status - ' . $e->getMessage());
            return response()->json(['origin' => 'TagController::status', 'message' => $e->getMessage()], 400);
        }
    }
}
