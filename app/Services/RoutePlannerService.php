<?php

namespace App\Services;
use App\Models\Flight;
use App\Models\Airport;

/**
 * A service class which create routes and plans for flights.
 *
 * @author  Vinh Truong
 * @license MIT
 */
class RoutePlannerService
{
    /**
     * Performs Database search and format the result according to the query value
     *
     * @param array $request The validated request.
     *
     * @return array $routes The planned flights with prices attached.
     */
    public function searchForRoutes(array $request) {
        $this->request = $request;
        $preferredAirline = $request['preferred_airline'] ?? null;

        // get flights: direct and indirect flights ( flights with transit)
        $routes = $this->getFlightsBetween($request['departure_airport'], $request['arrival_airport'], $preferredAirline);
        $flightsBack = [];

        if ($request['trip_type'] == 'round-trip') {
            // get returned flights
            $flightsBack = $this->getFlightsBetween($request['arrival_airport'], $request['departure_airport'], $preferredAirline);
        }

        $routes = $this->combineRouteTrips($routes, $flightsBack);

        // sort flights
        if (isset($request['sort_by'])) {
            $routes = $this->sortRoutes($routes);
        }

        return $routes;
    }

    /**
     * Matches flights and returned flight 1 by 1.
     *
     * @param array $flightsTo The direct flights.
     * @param array $flightsBack The returned flights.
     *
     * @return array $flightsTo The round-trip flights options.
     */
    protected function combineRouteTrips($flightsTo, $flightsBack)
    {
        foreach ($flightsTo as $key => $flightTo) {
            $flightsTo[$key]['flight'] = $this->formatFlightData($flightsTo[$key]['flight']);

            foreach ($flightsBack as $flightBack) {
                $flightsTo[$key]['flight'] = array_merge($flightsTo[$key], $this->formatFlightData($flightBack['flight'], true));
                $flightsTo[$key]['price'] = array_sum(array_column($flightsTo[$key]['flight'], 'price'));
            }
        }

        return $flightsTo;
    }

    /**
     * Get flight from point A to point B, with option of preferred airline.
     *
     * @param string $from The iata code of departure location.
     * @param string $to The iata code of arrival location.
     * @param string $preferredAirline Preferred airline name.
     *
     * @return array  All the flights from point A to point B.
     */
    protected function getFlightsBetween($from, $to, $preferredAirline)
    {
        $allFlights = Flight::getAllFlights($from, $to, $preferredAirline);

        $availableRoutes = [];

        foreach ($allFlights as $key => $flight) {
            // direct flights
            if ($flight['departure_airport'] == $from && $flight['arrival_airport'] == $to) {
                $availableRoutes[] = ['flight' => [$flight], 'price' => $flight['price']];
            }

            // indirect flights
            if ($flight['departure_airport'] == $from) {
                $availableRoutes[] = $this->getIndirectFlight($allFlights, $key);
            }
        }

        return array_filter($availableRoutes);
    }

    protected function getIndirectFlight($allFlights, $start)
    {
        $flightsRoute = [];

        $from = $allFlights[$start]['departure_airport'];
        $this->startTz = $allFlights[$start]['timezone'];
        $startTimeAtAirport = new \DateTime($allFlights[$start]['departure_datetime'], new \DateTimeZone($this->startTz));

        for ($i = $start; $i < count($allFlights); $i++)
        {
            $flight = $allFlights[$i];

            if ($flight['departure_airport'] == $from)
            {
                $currentFlightDepartureTime = $this->unifyTimeZone($flight['departure_datetime'], $flight['timezone']);
                $currentFlightArrivalTime = $this->unifyTimeZone($flight['arrival_datetime'], $flight['timezone']);

                if ($currentFlightDepartureTime >= $startTimeAtAirport) {
                    $flightsRoute[] = $flight;
                    $from = $flight['arrival_airport'];
                    $startTimeAtAirport = $currentFlightArrivalTime;
                }
            }
        }

        // when cant find flight, if route == 1 then dont return anything
        if (count($flightsRoute) <= 1) {
            return [];
        }

        return ['flight' => $flightsRoute, 'price' => array_sum(array_column($flightsRoute, 'price'))];
    }

    protected function unifyTimeZone($time, $arrivalTz)
    {
        $date = new \DateTime($time, new \DateTimeZone($arrivalTz));
        $date->setTimezone(new \DateTimeZone($this->startTz));

        return $date;
    }

    protected function sortRoutes($routes)
    {
        $sortOrder = $this->request['sort_order'];

        if ($this->request['sort_by'] == 'stops') {
            usort($routes, function($a, $b) use ($sortOrder) {
                return $sortOrder == 'asc' ? (count($a['flight']) > count($b['flight'])) : (count($b['flight']) > count($a['flight']));
            });
        }

        if ($this->request['sort_by'] == 'price') {
            usort($routes, function($a, $b) use ($sortOrder) {
                return $sortOrder == 'asc' ? (floatval($a['price']) > floatval($b['price'])) : (floatval($b['price']) > floatval($a['price']));
            });
        }

        if ($this->request['sort_by'] == 'trip-duration') {
            usort($routes, function($a, $b) use ($sortOrder) {
                $durationA = $this->getTotalFlightsDurationTime($a['flight']);
                $durationB = $this->getTotalFlightsDurationTime($b['flight']);

                return $sortOrder == 'asc' ? ($durationA > $durationB) : ($durationB > $durationA);
            });
        }

        return $routes;
    }


    protected function formatFlightData($flights, $isReturnedFlight = false)
    {
        $date = $isReturnedFlight ? $this->request['return_date'] : $this->request['departure_date'];

        foreach ($flights as $key => $flight) {
            unset($flights[$key]['timezone']);
            $flights[$key]['departure_datetime'] = $date.' '.$flights[$key]['departure_datetime'];
            $flights[$key]['arrival_datetime'] = $date.' '.$flights[$key]['arrival_datetime'];
        }

        return $flights;
    }

    protected function getTotalFlightsDurationTime($flights)
    {
        return array_reduce($flights, function ($carry, $val) {
            $carry += (strtotime($val['arrival_datetime']) - strtotime($val['departure_datetime']))/60;

            return $carry;
        }, 0);
    }
}
