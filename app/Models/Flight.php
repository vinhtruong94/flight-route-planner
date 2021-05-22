<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * A Model class which interacts with the flights table.
 *
 * @author  Vinh Truong
 * @license MIT
 */
class Flight extends Model
{
    use HasFactory;

    /**
     * Performs Database search to get all flights with requested departure and arrival airport names.
     *
     * @param string $departureCode The iata code of departure airport.
     * @param string $arrivalCode The iata code of arrival airport.
     * @param string $preferredAirline The preferred airline name.
     *
     * @return array $routes All the flight records.
     */
    public static function getAllFlights($departureCode, $arrivalCode, $preferredAirline) {
        $query = DB::table('flights AS f')
            ->join('airlines AS a', function($q) use ($preferredAirline) {
                $q->on('a.id', '=', 'f.airline_id');

                if (!is_null($preferredAirline)) {
                    $q->where('a.name', '=', "$preferredAirline");
                }
            })
            ->join('airports AS ap_departure', 'ap_departure.id', '=', 'f.departure_id')
            ->join('airports AS ap_arrival', 'ap_arrival.id', '=', 'f.arrival_id')
            ->select([
                'a.name AS airline',
                'f.number AS number',
                'ap_departure.iata_code AS departure_airport',
                'f.departure_time AS departure_datetime',
                'ap_arrival.iata_code AS arrival_airport',
                'f.arrival_time AS arrival_datetime',
                'f.price',
                'ap_departure.timezone',]
            )
            ->where('ap_departure.iata_code', '=', $departureCode)
            ->orWhere('ap_arrival.iata_code', '=', $arrivalCode);

        return $query->get()->map(function($item) { return (array) $item;});
    }
}
