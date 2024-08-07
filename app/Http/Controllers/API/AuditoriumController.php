<?php

namespace App\Http\Controllers\API;

use App\Models\Auditorium;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class AuditoriumController extends BaseController
{
    public function getAllAuditoria(Request $request, string $branch)
    {
        try {
            $auditorium = Auditorium::whereHas('cinemaBranch', function ($query) use ($branch) {
                $query->where('code', '=', $branch);
            })->select(['id', 'name'])->get();
            return $this->sendResponse($auditorium, 'Get all auditoria successfully.');
        } catch (\Exception $e) {
            Log::error($e);
            return $this->sendError('An error occurred during get all auditoria.', [], Response::HTTP_BAD_REQUEST);
        }
    }
}
