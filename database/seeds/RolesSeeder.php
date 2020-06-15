<?php

use App\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = Role::create([
            'name' => 'Admin',
            'slug' => 'admin',
            'permissions' => [
                'access-admin-panel',
                'manage-products',
                'manage-companies',
                'manage-categories',
                'manage-users',
                'manage-orders'
            ]
        ]);

        $client = Role::create([
            'name' => 'Client',
            'slug' => 'client',
            'permissions' => [
                'create-order',
                'cancel-order',
                'show-enabled-products',
                'show-enabled-companies',
                'sales-records'
            ]
        ]);
    }
}
