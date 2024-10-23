<?php

namespace App\Http\Controllers\API;

use App\Enums\SeatType;
use App\Http\Controllers\Controller;
use App\Models\Auditorium;
use App\Models\CinemaCompany;
use App\Models\Film;
use App\Models\Screening;
use App\Models\SeatingArrangement;
use App\Models\TicketOrderItem;
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

    public function getSeatingLayoutByShowtime(Request $request, string $showtimeId)
    {
        try {
            $showtime = Screening::with([
                'ticketPrices' => function ($query) {
                    $query->select('id', 'screening_id', 'seat_type', 'price');
                }
            ])
                ->find($showtimeId);

            if (!$showtime) {
                return $this->sendError('Showtime not found.', [], Response::HTTP_NOT_FOUND);
            }

            $auditorium = Auditorium::with(['cinemaBranch' => function ($query) {
                $query->select('id', 'name', 'address', 'code');
            }])->select('id', 'cinema_branch_id', 'name', 'capacity', 'seat_direction', 'code', 'columns', 'rows')
                ->find($showtime->auditorium_id);
            if (!$auditorium) {
                return $this->sendError('Auditorium not found.', [], Response::HTTP_NOT_FOUND);
            }

            $seatingLayout = SeatingArrangement::where('auditorium_id', '=', $auditorium->id)->get()->toArray();
            $ticketOrderItems = TicketOrderItem::whereHas('ticketOrder', function ($query) use ($showtimeId) {
                $query->where('screening_id', '=', $showtimeId);
            })->get()->toArray();

            foreach ($seatingLayout as $key => $seat) {
                if (in_array($seat['id'], array_column($ticketOrderItems, 'seating_arrangement_id'))) {
                    $seatingLayout[$key]['seat_type'] = SeatType::Sold->value;
                }
            }

            $groupedSeatingLayout = [];
            foreach ($seatingLayout as $seat) {
                $yPosition = $seat['y_position'];

                if (!isset($groupedSeatingLayout[$yPosition])) {
                    $groupedSeatingLayout[$yPosition] = [];
                }

                $groupedSeatingLayout[$yPosition][] =  (object) [
                    'id' => $seat['id'],
                    'label' => $seat['label'],
                    'seat_type' => $seat['seat_type'],
                    'x_position' => $seat['x_position'],
                ];
            }
            $groupedSeatingLayout = (object) $groupedSeatingLayout;
            $finalData = (object) [
                'seatingLayout' => $groupedSeatingLayout,
                'auditorium' => $auditorium,
                'ticketPrices' => $showtime->ticketPrices
            ];
            return $this->sendResponse($finalData, 'Get seating layout by showtime successfully.');
        } catch (\Exception $e) {
            Log::error($e);
            return $this->sendError('An error occurred during get seating layout by showtime', [], Response::HTTP_BAD_REQUEST);
        }
    }

    public function getShowtimesByFilm(Request $request, string $filmCode)
    {
        try {
            $provinceCode = $request->query('province');
            $date = $request->query('date');
            $film = Film::where('code', '=', $filmCode)->first();
            if (!$film) {
                return $this->sendError('Film not found', [], Response::HTTP_NOT_FOUND);
            }

            $dateTime = new DateTime($date);
            $startDate = $dateTime->setTime(0, 0, 0)->format('Y-m-d H:i:s');
            $endDate = $dateTime->setTime(23, 59, 59)->format('Y-m-d H:i:s');
            $showtimes = Screening::with('auditorium.cinemaBranch')->whereHas('film', function ($query) use ($filmCode) {
                $query->where('code', '=', $filmCode);
            })
                ->whereBetween('screening_time', [$startDate, $endDate])
                ->whereHas('auditorium.cinemaBranch.region', function ($query) use ($provinceCode) {
                    $query->where('code', '=', $provinceCode);
                })
                ->get();

            $convertData = [];

            foreach ($showtimes as $item) {
                $cinemaCompanyId = $item->auditorium->cinemaBranch->cinema_company_id;
                $branch = $item->auditorium->cinemaBranch;

                // Check if the cinema company already exists in the array
                if (!isset($convertData[$cinemaCompanyId])) {
                    $cinemaCompany = CinemaCompany::with(['logo' => function ($query) {
                        $query->select('id', 'url');
                    }])->select(['id', 'name', 'logo'])->find($cinemaCompanyId)->toArray();
                    if ($cinemaCompany) {
                        $convertData[$cinemaCompanyId] = [
                            'company' => $cinemaCompany,
                            'branches' => []
                        ];
                    }
                }

                // Check if the branch already exists in the company
                if (!isset($convertData[$cinemaCompanyId]['branches'][$branch->id])) {
                    $convertData[$cinemaCompanyId]['branches'][$branch->id] = [
                        'id' => $branch->id,
                        'name' => $branch->name,
                        'address' => $branch->address,
                        'region_id' => $branch->region_id,
                        'code' => $branch->code,
                        'showtimes' => [
                            'vietsub' => [],
                            'voiceover' => []
                        ]
                    ];
                }

                $filmTranslation = $item->film_translation;
                if ($filmTranslation === 'vietsub') {
                    $convertData[$cinemaCompanyId]['branches'][$branch->id]['showtimes']['vietsub'][] = [
                        'id' => $item->id,
                        'screening_time' => $item->screening_time,
                    ];
                }
                if ($filmTranslation === 'voiceover') {
                    $convertData[$cinemaCompanyId]['branches'][$branch->id]['showtimes']['voiceover'][] = [
                        'id' => $item->id,
                        'screening_time' => $item->screening_time,
                    ];
                }
            }

            // Convert the associative array to a numerically indexed array and sort showtimes
            $finalData = [];
            foreach ($convertData as $companyData) {
                $branches = [];
                foreach ($companyData['branches'] as $branchData) {
                    // Sort the showtimes for each translation type
                    $branchData['showtimes']['vietsub'] = collect($branchData['showtimes']['vietsub'])->sortBy('screening_time')->values()->all();
                    $branchData['showtimes']['voiceover'] = collect($branchData['showtimes']['voiceover'])->sortBy('screening_time')->values()->all();

                    $branches[] = $branchData;
                }

                $finalData[] = [
                    'company' => $companyData['company'],
                    'branches' => $branches
                ];
            }

            return $this->sendResponse($finalData, 'Get showtimes by film successfully.');
        } catch (\Exception $e) {
            Log::error($e);
            return $this->sendError('An error occurred during get showtimes by film.', [], Response::HTTP_BAD_REQUEST);
        }
    }
}
