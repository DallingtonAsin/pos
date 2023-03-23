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

        User::where('id', 1)->update(['first_name' => 'Dallington', 'last_name' => 'Asingwire', 'username' => 'admin', 'role_id' => 2]);
        User::where('id', 2)->update(['first_name' => 'Moses', 'last_name' => 'Arinda', 'username' => 'test002', 'role_id' => 1]);
        // User::where('id', 3)->update(['first_name' => 'Guest', 'last_name' => 'User', 'username' => 'test001', 'role_id' => 2]);


    }
}
