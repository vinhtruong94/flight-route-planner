<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AirlineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = new \DateTime();
        $currentTimeStamp = $now->format('Y-m-d H:i:s');

        DB::table('airlines')
        ->insert([
            ['iata_code' => 'AC','name' => 'Air Canada', "created_at" => $currentTimeStamp, "updated_at" => $currentTimeStamp],
            ['iata_code' => 'TS', 'name' => 'Air Transat', "created_at" => $currentTimeStamp, "updated_at" => $currentTimeStamp],
        ]);
    }
}
