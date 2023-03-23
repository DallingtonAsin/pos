<?php

// namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        Role::create(['name' => 'Cashier', 'is_admin' => 0, 'is_super_admin' => 0]);
        Role::create(['name' => 'Administrator', 'is_admin' => 1, 'is_super_admin' => 0]);

    }
}
