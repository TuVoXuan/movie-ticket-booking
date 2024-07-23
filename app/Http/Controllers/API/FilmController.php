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
                return redirect()->route('films.index')->with('error', 'Film not found.');
            }

            return $this->sendResponse($film, 'Get film details successfully.');
        } catch (\Exception $e) {
            Log::error($e);
            $this->sendError('An error occurred during get film details', [], Response::HTTP_BAD_GATEWAY);
        }
    }
}
