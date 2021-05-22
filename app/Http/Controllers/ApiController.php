<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoutePlanRequest;
use App\Services\RoutePlannerService;

/**
 * A simple controller class which search for routes based on user request.
 *
 * @author  Vinh Truong
 * @license MIT
 */
class ApiController extends Controller
{
    public function __construct(RoutePlannerService $routePlannerService)
    {
        $this->routePlannerService = $routePlannerService;
    }

    public function searchForRoutes(RoutePlanRequest $request)
    {
        $validated = $request->validated();
        $records = $this->routePlannerService->searchForRoutes($validated);

        if (empty($records)) {
            return response()->json('No results found', 404);
        }

        return response()->json($this->formatResponse($request, $records), 200);
    }

    protected function formatResponse($request, $records) {
        return ['request' => $request->all(), 'response' => ['trips' => $records]];
    }
}
