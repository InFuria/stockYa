<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function select()
    {
        try {

            return response()->json(request()->user(), 200);

        } catch (\Exception $e){
            Log::error('UserController::select - ' . $e->getMessage());
            return response()->json(['origin' => 'UserController::select', 'message' => $e->getMessage()], 400);
        }
    }

    public function store(UserRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = new User([
                'dni'     => $request->dni,
                'username'     => $request->username,
                'name'     => $request->name,
                'address'     => $request->address,
                'phone'     => $request->phone,
                'status'     => 1,
                'email'    => $request->email,
                'password' => Hash::make($request->password)
            ]);
            DB::commit();

            return response()->json([
                'message' => 'Se ha creado al usuario!',
                'user' => $user
            ], 200);

        } catch (\Exception $e){
            DB::rollBack();
            Log::error('UserController:: - ' . $e->getMessage());
            return response()->json(['origin' => 'UserController::store', 'message' => $e->getMessage()], 400);
        }
    }

    public function update(UserRequest $request, User $user)
    {
        DB::beginTransaction();
        try {
            $user->update($request->all());
            $user->saveOrFail();
            DB::commit();

            return response()->json([
                'message' => 'Se ha actualizado el usuario!',
                'user' => $user
            ],200);

        } catch (\Exception $e){
            DB::rollBack();
            Log::error('UserController:: - ' . $e->getMessage());
            return response()->json(['origin' => 'UserController::update', 'message' => $e->getMessage()], 400);
        }
    }

    public function destroy(User $user)
    {
        DB::beginTransaction();
        try {
            $user->delete();
            $user->saveOrFail();
            DB::commit();

            return response()->json([
                'message' => 'El usuario ha sido eliminado!'
            ], 200);
        } catch (\Exception $e){
            DB::rollBack();
            Log::error('UserController:: - ' . $e->getMessage());
            return response()->json(['origin' => 'UserController::destroy', 'message' => $e->getMessage()], 400);
        }
    }

    public function ban(User $user){
        DB::beginTransaction();
        try {

            $user->status = $user->status == 1 ? 0 : 1;
            $user->save();
            DB::commit();

            return response()->json([
                'message' => 'El estado del usuario ha sido cambiado!'
            ], 200);

        } catch (\Exception $e){
            DB::rollBack();
            Log::error('UserController:: - ' . $e->getMessage());
            return response()->json(['origin' => 'UserController::ban', 'message' => $e->getMessage()], 400);
        }
    }
}
