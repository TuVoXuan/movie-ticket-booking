<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Screening;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class ShowtimesController extends BaseController
{
    public function getShowtimesByDateAndCinemaBranch(Request $request, string $branch, string $date)
    {
        try {
            $dateTime = new DateTime($date);
            $startDate = $dateTime->setTime(0, 0, 0)->format('Y-m-d H:i:s');
            $endDate = $dateTime->setTime(23, 59, 59)->format('Y-m-d H:i:s');
            $showtimes = Screening::with([
                'film' => function ($query) {
                    $query->select('id', 'title', 'duration', 'trailer', 'thumbnail', 'code', 'age_restricted');
                },
                'film.thumbnail' => function ($query) {
                    $query->select('id', 'url');
                }
            ])
                ->whereHas('auditorium.cinemaBranch', function ($query) use ($branch) {
                    $query->where('code', '=', $branch);
                })
                ->whereBetween('screening_time', [Carbon::parse($startDate), Carbon::parse($endDate)])
                ->select('id', 'screening_time', 'film_translation', 'film_id')
                ->get();

            $structuredData = [];

            foreach ($showtimes as $showtime) {
                $filmId = $showtime->film->id;

                if (!isset($structuredData[$filmId])) {
                    $structuredData[$filmId] = [
                        'film' => $showtime->film,
                        'showtimes' => []
                    ];
                }

                $translation = $showtime->film_translation;
                if (!isset($structuredData[$filmId]['showtimes'][$translation])) {
                    $structuredData[$filmId]['showtimes'][$translation] = [];
                }

                $structuredData[$filmId]['showtimes'][$translation][] = [
                    'id' => $showtime->id,
                    'screening_time' => $showtime->screening_time,
                ];
            }
            $finalData = array_values($structuredData);
            return $this->sendResponse($finalData, 'Get showtimes by date and cinema branch successfully.');
        } catch (\Exception $e) {
            Log::error($e);
            return $this->sendError('An error occurred during get list regions', [], Response::HTTP_BAD_REQUEST);
        }
    }
}
