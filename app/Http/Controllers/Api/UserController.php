<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\User;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{

    public function all()
    {
        try {
            /*if ($input = request('name')->input()){
                dd($input);
            }*/

            $users = User::all();

            return response()->json($users);

        } catch (\Exception $e){
            Log::error('UserController::all - ' . $e->getMessage());
            return response('Ha ocurrido un error.', 400)->json(['message' => $e->getMessage()]);
        }
    }

    public function create()
    {
        try {

        } catch (\Exception $e){
            Log::error('UserController:: - ' . $e->getMessage());
            return response('Ha ocurrido un error.', 400)->json(['message' => $e->getMessage()]);
        }
    }

    public function store(UserRequest $request)
    {
        try {

        } catch (\Exception $e){
            Log::error('UserController:: - ' . $e->getMessage());
            return response('Ha ocurrido un error.', 400)->json(['message' => $e->getMessage()]);
        }
    }

    public function show(User $user)
    {
        try {

        } catch (\Exception $e){
            Log::error('UserController:: - ' . $e->getMessage());
            return response('Ha ocurrido un error.', 400)->json(['message' => $e->getMessage()]);
        }
    }

    public function edit(User $user)
    {
        try {

        } catch (\Exception $e){
            Log::error('UserController:: - ' . $e->getMessage());
            return response('Ha ocurrido un error.', 400)->json(['message' => $e->getMessage()]);
        }
    }

    public function update(UserRequest $request, User $user)
    {
        try {

        } catch (\Exception $e){
            Log::error('UserController:: - ' . $e->getMessage());
            return response('Ha ocurrido un error.', 400)->json(['message' => $e->getMessage()]);
        }
    }

    public function destroy(User $user)
    {
        try {

        } catch (\Exception $e){
            Log::error('UserController:: - ' . $e->getMessage());
            return response('Ha ocurrido un error.', 400)->json(['message' => $e->getMessage()]);
        }
    }

    public function ban(User $user){
        try {

        } catch (\Exception $e){
            Log::error('UserController:: - ' . $e->getMessage());
            return response('Ha ocurrido un error.', 400)->json(['message' => $e->getMessage()]);
        }
    }
}
