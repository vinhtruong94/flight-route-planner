<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * A request validator class to validate user request.
 *
 * @author  Vinh Truong
 * @license MIT
 */
class RoutePlanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "departure_airport" => "required|size:3",
            "arrival_airport" => "required|size:3",
            "departure_date" => "required|date",
            "return_date" => "required-if:trip_type,==,round-trip",
            "trip_type" => "required|in:round-trip,one-way",
            "preferred_airline" => "nullable",
            "sort_by" => 'nullable|in:price,stops,trip-duration',
            "sort_order" => 'required-with:sort_by|in:asc,desc'
        ];
    }
}
