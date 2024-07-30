<?php

namespace App\Http\Controllers\API;

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
            $this->sendError('An error occurred during get region by code', [], Response::HTTP_BAD_GATEWAY);
        }
    }
}
