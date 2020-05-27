<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\User;

class UserController extends Controller
{

    public function index()
    {
        if ($input = request('name')->input()){
            dd($input);
        }

        return view('users.index');
    }

    public function create()
    {
        //
    }

    public function store(UserRequest $request)
    {
        //
    }

    public function show(User $user)
    {
        //
    }

    public function edit(User $user)
    {
        //
    }

    public function update(UserRequest $request, User $user)
    {
        //
    }

    public function destroy(User $user)
    {
        //
    }

    public function ban(User $user){
        //
    }
}
