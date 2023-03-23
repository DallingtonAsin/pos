<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        $this->call([
            RoleTableSeeder::class,
            UserTableSeeder::class,
            // SuppliersTableSeeder::class,
            // StockCategoriesTableSeeder::class,
            // StockTableSeeder::class,
            // CustomersTableSeeder::class,
            // DamagesTableSeeder::class,
            // EventsTableSeeder::class,
            // ExpensesTableSeeder::class,
            // SalesTableSeeder::class,
            // PurchasesTableSeeder::class,
        ]);
    }
}
