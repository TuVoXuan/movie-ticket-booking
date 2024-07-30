<?php

namespace App\Http\Controllers\API;

use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class RegionController extends BaseController
{
    public function getList(Request $request)
    {
        try {
            $search = $request->query('search');
            $pageSize = $request->query('page_size', 10);

            $regions = Region::when($search, function ($query, $search) {
                $query->where('name', 'LIKE', '%' . $search . '%');
            })->select(['id', 'name', 'code'])
                ->paginate($pageSize);

            return $this->sendResponse($regions, 'Get list regions successfully.');
        } catch (\Exception $e) {
            Log::error($e);
            $this->sendError('An error occurred during get list regions', [], Response::HTTP_BAD_GATEWAY);
        }
    }

    public function findByCode(Request $request, string $code)
    {
        try {
            $region = Region::where('code', '=', $code)->first();
            if (!$region) {
                return $this->sendError('Region not found', [], Response::HTTP_NOT_FOUND);
            }

            return $this->sendResponse($region, 'Find region by code successfully.');
        } catch (\Exception $e) {
            Log::error($e);
            $this->sendError('An error occurred during get region by code', [], Response::HTTP_BAD_GATEWAY);
        }
    }
}
