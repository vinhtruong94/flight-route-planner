<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AirportSeeder extends Seeder
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

        DB::table("airports")->insert([
            [
                "name" => "Pierre Elliott Trudeau International",
                "city" => "Montreal",
                "iata_code" => "YUL",
                "city_code" => "YMQ",
                "country_code" => "CA",
                "region_code" => "QC",
                "latitude" => '45.457714',
                "longitude" => '-73.749908',
                "timezone" => "America/Montreal",
                "created_at" => $currentTimeStamp,
                "updated_at" => $currentTimeStamp
            ],
            [
                "name" => "Toronto City Centre Airport",
                "city" => "Toronto",
                "iata_code" => "YTZ",
                "city_code" => "YTO",
                "country_code" => "CA",
                "region_code" => "ON",
                "latitude" => '43.627499',
                "longitude" => '-79.396167',
                "timezone" => "America/Toronto",
                "created_at" => $currentTimeStamp,
                "updated_at" => $currentTimeStamp
            ],
            [
                "name" => "Vancouver Intl Arpt",
                "city" => "Vancouver",
                "iata_code" => "YVR",
                "city_code" => "YVR",
                "country_code" => "CA",
                "region_code" => "BC",
                "latitude" => "49.193889",
                "longitude" => "-123.184444",
                "timezone" => "America/Vancouver",
                "created_at" => $currentTimeStamp,
                "updated_at" => $currentTimeStamp
            ],
        ]);
    }
}
