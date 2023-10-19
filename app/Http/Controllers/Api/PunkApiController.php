<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class PunkApiController extends Controller
{
    /**
     * Get a paginated list of beers from Punk API.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $page = (int)$request->get('page', 1);
            $perPage = (int)$request->get('perPage', 20);
            $response = Http::get(sprintf('%s/beers/?page=%u&per_page=%u', config('cloudcare.punk_api.endpoint'), $page, $perPage));
            $response->throw();

            return response()->json($response->json());
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_SERVICE_UNAVAILABLE);
        }
    }
}
