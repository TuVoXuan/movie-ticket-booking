<?php

namespace App\Http\Controllers\API;

use App\Models\Film;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class FilmController extends BaseController
{
    public function getFilmDetails(string $id)
    {
        try {
            $film = Film::with(['genres', 'directors', 'producers', 'actors', 'thumbnail', 'thumbnailBg'])->find($id);
            if (!$film) {
                return $this->sendError('Film not found', [], Response::HTTP_NOT_FOUND);
            }

            return $this->sendResponse($film, 'Get film details successfully.');
        } catch (\Exception $e) {
            Log::error($e);
            $this->sendError('An error occurred during get film details', [], Response::HTTP_BAD_GATEWAY);
        }
    }

    public function getListOptionsFilm(Request $request)
    {
        try {
            $search = $request->query('search');
            $pageSize = $request->query('page_size', 10);

            $films = Film::with('thumbnail')->when($search, function ($query, $search) {
                $query->where('title', 'LIKE', '%' . $search . '%');
            })->select(['id', 'thumbnail', 'code', 'title'])
                ->paginate($pageSize);
            return $this->sendResponse($films, 'Get list options films successfully.');
        } catch (\Exception $e) {
            Log::error($e);
            $this->sendError('An error occurred during get list options films.', [], Response::HTTP_BAD_REQUEST);
        }
    }
}
