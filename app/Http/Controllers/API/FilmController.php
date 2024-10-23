<?php

namespace App\Http\Controllers\API;

use App\Models\Film;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class FilmController extends BaseController
{
    public function getFilmDetails(string $idOrCode)
    {
        try {
            $film = Film::with(['genres', 'directors', 'producers', 'actors', 'thumbnail', 'thumbnailBg'])->where('id', $idOrCode)
                ->orWhere('code', $idOrCode)
                ->first();

            if (!$film) {
                return $this->sendError('Film not found', [], Response::HTTP_NOT_FOUND);
            }

            return $this->sendResponse($film, 'Get film details successfully.');
        } catch (\Exception $e) {
            Log::error($e);
            return $this->sendError('An error occurred during get film details', [], Response::HTTP_BAD_GATEWAY);
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
            return  $this->sendError('An error occurred during get list options films.', [], Response::HTTP_BAD_REQUEST);
        }
    }

    public function getFilmsShowing(Request $request)
    {
        try {
            $dateTime = new DateTime();
            $startDate = $dateTime->setTime(0, 0, 0)->format('Y-m-d H:i:s');
            $endDate = $dateTime->setTime(23, 59, 59)->format('Y-m-d H:i:s');

            $films = Film::whereHas('screenings', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('screening_time', [$startDate, $endDate]);
            })->with(['thumbnail' => function ($query) {
                $query->select('id', 'url');
            }])
                ->select('id', 'release_date', 'thumbnail', 'title', 'code')
                ->get();

            return $this->sendResponse($films, 'Get list films showing successfully.');
        } catch (\Exception $e) {
            Log::error($e);
            return  $this->sendError('An error occurred during get films showing.', [], Response::HTTP_BAD_REQUEST);
        }
    }
}
