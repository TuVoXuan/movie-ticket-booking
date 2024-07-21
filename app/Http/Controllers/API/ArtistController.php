<?php

namespace App\Http\Controllers\API;

use App\Models\Artist;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class ArtistController extends BaseController
{
    public function getList(Request $request)
    {
        try {
            $search = $request->query('search');
            $pageSize = $request->query('page_size', 10);

            $artists = Artist::when($search, function ($query, $search) {
                $query->where('name', 'LIKE', '%' . $search . '%');
            })->select(['id', 'name'])
                ->paginate($pageSize);

            return $this->sendResponse($artists, 'Get list artists successfully.');
        } catch (\Exception $e) {
            Log::error($e);
            $this->sendError('An error occurred during get list artist', [], Response::HTTP_BAD_GATEWAY);
        }
    }
}
