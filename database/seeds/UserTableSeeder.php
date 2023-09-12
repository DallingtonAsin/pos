<?php

// namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->count(2)->create();

        User::where('id', 2)->update(['first_name' => 'Cashier', 'last_name' => 'Tester', 'username' => 'cashier', 'role_id' => 1]);
        User::where('id', 1)->update(['first_name' => 'Admin', 'last_name' => 'Tester', 'username' => 'admin', 'role_id' => 2]);

    }
}
