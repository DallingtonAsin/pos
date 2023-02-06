<?php

// namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class SalesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Sale::factory()->count(200)->create();

        $sales = Sale::get();
        foreach($sales as $sale){
            DB::update(
                "UPDATE sales SET date=DATE_FORMAT(date,'2023-%m-%d %T')"
            );
        }

    }
}
