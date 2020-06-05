<?php

namespace App\Policies;

use App\User;
use App\WebSale;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function isAdmin(User $user){
        return $user->email == 'eli_gimenez@outlook.com' || $user->email == 'learfen001@gmail.com';
    }

    public function isOwner(User $user, $order){
        return $user->id == $order->client_id;
    }
}
