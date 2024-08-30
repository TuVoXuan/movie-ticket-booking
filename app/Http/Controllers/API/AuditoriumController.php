<?php

namespace App\Http\Controllers\API;

use App\Models\Auditorium;
use App\Models\SeatingArrangement;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuditoriumController extends BaseController
{
    public function getAllAuditoria(Request $request, string $branch)
    {
        try {
            $auditorium = Auditorium::whereHas('cinemaBranch', function ($query) use ($branch) {
                $query->where('code', '=', $branch);
            })->select(['id', 'name', 'code'])->get();
            return $this->sendResponse($auditorium, 'Get all auditoria successfully.');
        } catch (\Exception $e) {
            Log::error($e);
            return $this->sendError('An error occurred during get all auditoria.', [], Response::HTTP_BAD_REQUEST);
        }
    }

    public function getByCode(Request $request, string $branch, string $auditorium)
    {
        try {
            $auditoriumFound = Auditorium::whereHas('cinemaBranch', function ($query) use ($branch) {
                $query->where('code', '=', $branch);
            })->where('code', '=', $auditorium)->select(['id', 'name', 'code'])->first();

            return $this->sendResponse($auditoriumFound, 'Get all auditoria successfully.');
        } catch (\Exception $e) {
            Log::error($e);
            return $this->sendError('An error occurred during get all auditoria.', [], Response::HTTP_BAD_REQUEST);
        }
    }

    public function getSeatTypes(Request $request, string $auditoriumId)
    {
        try {
            $seatTypes = SeatingArrangement::select('seat_type', DB::raw('count(*) as total_seats'))
                ->where('auditorium_id', $auditoriumId)
                ->where('seat_type', 'like', 'seat%')
                ->groupBy('seat_type')
                ->get();

            return $this->sendResponse($seatTypes, 'Get seat type auditorium has successfully.');
        } catch (\Exception $e) {
            Log::error($e);
            return $this->sendError('An error occurred during get seat type auditorium has.', [], Response::HTTP_BAD_REQUEST);
        }
    }
}
