<?php

namespace App\Providers;

use App\Product;
use Carbon\Carbon;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;
use App\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        $this->registerWebPolicies();

        Passport::routes();
        Passport::tokensExpireIn(Carbon::now()->setTimezone('America/Asuncion')->addMinutes(30));
        //Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(Carbon::now()->setTimezone('America/Asuncion')->addHours(24));
    }

    public function registerWebPolicies()
    {
        Gate::define('isAdmin', function ($user) {
            return $user->inRole('admin');
        });

        Gate::define('isOwner', function (User $user, $order) {
            return $user->id == $order->client_id;
        });

        Gate::define('manage-products', function ($user) {
            return $user->hasAccess(['manage-products']);
        });

        Gate::define('manage-companies', function ($user) {
            return $user->hasAccess(['manage-companies']);
        });

        Gate::define('manage-users', function ($user) {
            return $user->hasAccess(['manage-users']);
        });

        Gate::define('manage-orders', function ($user) {
            return $user->hasAccess(['manage-orders']);
        });
    }
}
