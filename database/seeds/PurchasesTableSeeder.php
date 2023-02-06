<?php

// namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;

class PurchasesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Purchase::factory()->count(250)->create();
        $purchases = Purchase::get();
        foreach($purchases as $purchase){
            DB::update(
                "UPDATE purchases SET date=DATE_FORMAT(date,'2023-%m-%d %T')"
            );
            DB::update(
                "UPDATE purchases SET date_of_purchase=DATE_FORMAT(date_of_purchase,'2023-%m-%d %T')"
            );
        }

    }
}
