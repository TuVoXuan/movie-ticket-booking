<?php

namespace App\Http\Controllers\API;

use App\Models\CinemaBranch;
use App\Models\CinemaCompany;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class CinemaController extends BaseController
{
    public function getAllCompany()
    {
        return $this->sendResponse(
            CinemaCompany::with(['logo'])->get(),
            'Get all cinema companies successfully.'
        );
    }

    public function findByCode(Request $request, string $code)
    {
        try {
            $company = CinemaCompany::where('code', '=', $code)->select(['id', 'name', 'code'])->first();
            if (!$company) {
                return $this->sendError('Cinema company not found.', []);
            }

            return $this->sendResponse($company, 'Find cinema company by code successfully.');
        } catch (\Exception $e) {
            Log::error($e);
            return $this->sendError('An error occurred during get region by code', [], Response::HTTP_BAD_GATEWAY);
        }
    }

    public function getAllCinemaBranchByRegion(Request $request, string $region)
    {
        try {
            $cinemaBranches = CinemaCompany::with([
                'logo' => function ($query) {
                    $query->select('id', 'url');
                },
                'branches' => function ($query) use ($region) {
                    $query
                        ->whereHas('region', function ($subQuery) use ($region) {
                            $subQuery->where('code', '=', $region);
                        })
                        ->select('id', 'name', 'code', 'cinema_company_id', 'address');
                }
            ])
                ->whereHas('branches', function ($query) use ($region) {
                    $query->whereHas('region', function ($subQuery) use ($region) {
                        $subQuery->where('code', '=', $region);
                    });
                })
                ->select('id', 'name', 'code', 'logo')->get();
            return $this->sendResponse($cinemaBranches, 'Get all cinema branches by region successfully.');
        } catch (\Exception $e) {
            Log::error($e);
            return $this->sendError('An error occurred during get cinema branches by region', [], Response::HTTP_BAD_GATEWAY);
        }
    }
}
