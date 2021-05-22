<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Airport;
use App\Models\Airline;

class FlightSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    private $sampleFlightsInfo = [
        'ON-BC' => ['price' => 350,'duration' => 285],
        'BC-ON' => ['price' => 380,'duration' => 285],
        'QC-BC' => ['price' => 300,'duration' => 315],
        'BC-QC' => ['price' => 320,'duration' => 315],
        'QC-ON' => ['price' => 100,'duration' => 70],
        'ON-QC' => ['price' => 120,'duration' => 70],
    ];

    private $baseFlightNumbers = [
        'QC' => 100,
        'ON' => 200,
        'BC' => 300,
    ];

    private $addToBase = 1;

    public function run()
    {
        // for the sake of simplicity, allow two time schedule only
        $startTimeList = ['07:00', '15:00'];
        $airports = Airport::all(['id', 'region_code', 'name'])->toArray();
        $this->insertData = [];

        foreach ($airports as $depart) {
            $this->addToBase = 1;

            foreach ($airports as $arrival) {
                if ($arrival['region_code'] == $depart['region_code']) {
                    continue;
                }
                $this->createFlightRecords($startTimeList, $depart, $arrival);
            }
        }

        DB::table('flights')->insert($this->insertData);
    }

    protected function createFlightRecords($startTimeList, $depart, $arrival)
    {
        $now = new \DateTime();
        $currentTimeStamp = $now->format('Y-m-d H:i:s');
        $airlines = Airline::all()->toArray();

        foreach ($airlines as $key => $airline) {
            foreach ($startTimeList as $startTime) {
                $key = $depart['region_code'].'-'.$arrival['region_code'];
                $flightNo = $this->baseFlightNumbers[$depart['region_code']] + $this->addToBase;
                $this->addToBase++;
                $arrivalTime = date('H:i', strtotime(sprintf("+%s minutes", $this->sampleFlightsInfo[$key]['duration']), strtotime($startTime)));
                $this->insertData[] =
                [
                    'number' => $flightNo,
                    'departure_time' => $startTime,
                    'arrival_time' => date('H:i', strtotime(sprintf("+%s minutes", $this->sampleFlightsInfo[$key]['duration']), strtotime($startTime))),
                    'price' => $this->sampleFlightsInfo[$key]['price'],
                    'airline_id' => $airline['id'],
                    'departure_id' => $depart['id'],
                    'arrival_id' => $arrival['id'],
                    'created_at' => $currentTimeStamp,
                    'updated_at' => $currentTimeStamp
                ];
            }
        }
    }

    protected function generateFlightNumber($location, $addToBase)
    {
        return $this->baseFlightNumbers[$location] + $this->addToBase;
    }
}
