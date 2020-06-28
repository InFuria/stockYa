<?php

namespace App\Http\Controllers\Api;

use App\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    public function getRoles()
    {
        try {

            $roles = Role::orderBy('slug', 'desc')->paginate(50);

            return response()->json($roles, 200);

        } catch (\Exception $e){
            Log::error('RoleController::getRoles - ' . $e->getMessage());
            return response()->json(['origin' => 'RoleController::getRoles', 'message' => $e->getMessage()], 400);
        }
    }

    public function select(Role $role)
    {
        try {

            return response()->json($role, 200);

        } catch (\Exception $e){
            Log::error('RoleController::select - ' . $e->getMessage());
            return response()->json(['origin' => 'RoleController::select', 'message' => $e->getMessage()], 400);
        }
    }

    public function store(RoleRequest $request)
    {
        DB::beginTransaction();
        try {

            $role = Role::create($request->all());

            DB::commit();

            return response()->json([
                'message' => 'El Rol ha sido registrado con exito!',
                'role' => $role], 201);

        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error('RoleController::store - ' . $qe->getMessage());
            return response()->json(['request'=>$request,'origin' => 'RoleController::store > db', 'message' => $qe->getMessage()], 400);
        } catch (\Exception $e){
            Log::error('RoleController::store - ' . $e->getMessage());
            return response()->json(['request'=>$request,'origin' => 'RoleController::store', 'message' => $e->getMessage()], 400);
        }
    }

    public function update(Request $request, Role $role)
    {
        DB::beginTransaction();
        try {

            $request->validate([
                'name' => 'string',
                'slug' => 'string',
                'permissions' => 'array'
            ]);

            $role->update($request->all());

            DB::commit();

            return response()->json([
                'message' => 'El Rol ha sido actualizado con exito!',
                'role' => $role], 200);

        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error('RoleController::update - ' . $qe->getMessage());
            return response()->json(['request'=>$request,'origin' => 'RoleController::update > db', 'message' => $qe->getMessage()], 400);
        } catch (\Exception $e){
            Log::error('RoleController::update - ' . $e->getMessage());
            return response()->json(['request'=>$request,'origin' => 'RoleController::update', 'message' => $e->getMessage()], 400);
        }
    }

    public function destroy(Role $role)
    {
        DB::beginTransaction();
        try {

            $role->users()->delete();
            $role->delete();
            DB::commit();

            return response('El rol seleccionado ha sido eliminado', 200);

        } catch (QueryException $qe) {
            DB::rollBack();
            Log::error('RoleController::destroy - ' . $qe->getMessage());
            return response()->json(['origin' => 'RoleController::destroy', 'message' => $qe->getMessage()], 400);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('RoleController::destroy - ' . $e->getMessage());
            return response()->json(['origin' => 'RoleController::destroy', 'message' => $e->getMessage()], 400);
        }
    }
}
