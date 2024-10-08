<?php

namespace App\Http\Controllers;

use App\Enums\FilmTranslation;
use App\Enums\SeatType;
use App\Models\Auditorium;
use App\Models\CinemaBranch;
use App\Models\Screening;
use App\Models\TicketPrice;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ShowtimesController extends Controller
{
    public function index(Request $request, string $branch)
    {
        try {
            $search = $request->query('search');
            $pageSize = $request->query('page_size', 10);
            $sort = $request->query('sort');
            $sort_order = $request->query('sort_order', 'asc');
            $screening_date = $request->query('screening_date');
            $auditorium = $request->query('auditorium');

            $auditoria = Auditorium::whereHas('cinemaBranch', function ($query) use ($branch) {
                $query->where('code', '=', $branch);
            })->when($auditorium, function ($query) use ($auditorium) {
                $query->where('code', '=', $auditorium);
            })->select(['id'])->get();

            $auditoriaIds = array_map(function ($item) {
                return $item['id'];
            }, $auditoria->toArray());

            $showtimes = Screening::with([
                'film' => function ($query) {
                    $query->select('id', 'title', 'thumbnail');
                },
                'film.thumbnail' => function ($query) {
                    $query->select('id', 'url');
                },
                'auditorium' => function ($query) {
                    $query->select('id', 'name');
                }
            ])
                ->whereHas('auditorium', function ($query) use ($auditoriaIds) {
                    $query->whereIn('auditorium_id', $auditoriaIds);
                })->when($search, function ($query, $search) {
                    $query->where('title', 'LIKE', '%' . $search . '%');
                })->when($sort, function ($query, $sort) use ($sort_order) {
                    $query->orderBy($sort, $sort_order);
                }, function ($query) {
                    $query->orderBy('created_at', 'desc');
                })->when($screening_date, function ($query) use ($screening_date) {
                    $date = new DateTime($screening_date);
                    $startDate = $date->setTime(0, 0, 0)->format('Y-m-d H:i:s');
                    $endDate = $date->setTime(23, 59, 59)->format('Y-m-d H:i:s');

                    $query->whereBetween('screening_time', [Carbon::parse($startDate), Carbon::parse($endDate)]);
                })->paginate($pageSize);

            $cinemaBranch = CinemaBranch::where('code', '=', $branch)->first();

            return Inertia::render('Cinemas/Showtimes/Index', ['showtimes' => $showtimes, 'cinemaBranchName' => $cinemaBranch->name]);
        } catch (\Throwable $th) {
            Log::error($th);
            return redirect()->route('cinemas.branches.showtimes.index', ['branch' => $branch])->with('error', 'An error occurred during get list showtimes.');
        }
    }

    public function create(Request $request, string $branch)
    {
        $cinemaBranch = CinemaBranch::where('code', '=', $branch)->first();

        return Inertia::render('Cinemas/Showtimes/CreateAndUpdate', ['cinemaBranchName' => $cinemaBranch->name]);
    }

    public function store(Request $request, string $branch)
    {
        try {
            $body = $request->all();
            $validated = Validator::make($body, [
                'film' => 'required|numeric|exists:films,id',
                'auditorium' => 'required|numeric|exists:auditoria,id',
                'screening_time' => 'required|date',
                'film_translation' => ['required', Rule::enum(FilmTranslation::class)],
                'normal_seat_price' => 'nullable|numeric',
                'vip_seat_price' => 'nullable|numeric'
            ]);

            if ($validated->fails()) {
                return back()->withErrors($validated->errors());
            }

            $newScreening = Screening::create([
                'film_id' => $body['film'],
                'auditorium_id' => $body['auditorium'],
                'screening_time' => Carbon::parse($body['screening_time']),
                'film_translation' => $body['film_translation']
            ]);

            if ($request->has('normal_seat_price')) {
                TicketPrice::create([
                    'screening_id' => $newScreening->id,
                    'seat_type' => SeatType::Normal->value,
                    'price' => $body['normal_seat_price']
                ]);
            }
            if ($request->has('vip_seat_price')) {
                TicketPrice::create([
                    'screening_id' => $newScreening->id,
                    'seat_type' => SeatType::VIP->value,
                    'price' => $body['vip_seat_price']
                ]);
            }

            return redirect()->route('cinemas.branches.showtimes.index', ['branch' => $branch])->with('success', 'Create new screening successfully.');
        } catch (\Throwable $th) {
            Log::error($th);
            return back()->with('error', 'And error occurred during create screening.');
        }
    }

    public function edit(Request $request, string $branch, string $showtime)
    {
        try {
            $screening = Screening::with([
                'film' => function ($query) {
                    $query->select('id', 'title', 'code', 'thumbnail');
                },
                'film.thumbnail' => function ($query) {
                    $query->select('id', 'url');
                },
                'auditorium' => function ($query) {
                    $query->select('id', 'name');
                },
                'ticketPrices' => function ($query) {
                    $query->select('id', 'screening_id', 'seat_type', 'price');
                }
            ])->whereHas('auditorium.cinemaBranch', function ($query) use ($branch) {
                $query->where('code', '=', $branch);
            })->where('id', '=', $showtime)->first();

            if (!$screening) {
                return redirect()->route('cinemas.branches.showtimes.index', ['branch' => $branch])->with('error', 'Showtimes not found.');
            }

            $cinemaBranch = CinemaBranch::where('code', '=', $branch)->first();

            return Inertia::render('Cinemas/Showtimes/CreateAndUpdate', ['screening' => $screening, 'cinemaBranchName' => $cinemaBranch->name]);
        } catch (\Throwable $th) {
            Log::error($th);
            return back()->with('error', 'And error occurred during get screening.');
        }
    }

    public function update(Request $request, string $branch, string $showtime)
    {
        try {
            $body = $request->all();
            $validated = Validator::make($body, [
                'film' => 'required|numeric|exists:films,id',
                'auditorium' => 'required|numeric|exists:auditoria,id',
                'screening_time' => 'required|date',
                'film_translation' => ['required', Rule::enum(FilmTranslation::class)],
                'normal_seat_price' => 'nullable|numeric',
                'vip_seat_price' => 'nullable|numeric'
            ]);

            if ($validated->fails()) {
                return back()->withErrors($validated->errors());
            }

            $screening = Screening::whereHas('auditorium.cinemaBranch', function ($query) use ($branch) {
                $query->where('code', '=', $branch);
            })->where('id', '=', $showtime)->first();

            if (!$screening) {
                return back()->with('error', 'Screening not found.');
            }

            $screening->update([
                'film_id' => $body['film'],
                'auditorium_id' => $body['auditorium'],
                'screening_time' => Carbon::parse($body['screening_time']),
                'film_translation' => $body['film_translation']
            ]);
            $this->updateOrCreateTicketPrice($showtime, SeatType::Normal, $request, $body, 'normal_seat_price');
            $this->updateOrCreateTicketPrice($showtime, SeatType::VIP, $request, $body, 'vip_seat_price');

            return redirect()->route('cinemas.branches.showtimes.index', ['branch' => $branch])->with('success', 'Update showtime successfully.');
        } catch (\Throwable $th) {
            Log::error($th);
            return back()->with('error', 'And error occurred during update screening.');
        }
    }

    function updateOrCreateTicketPrice($showtime, $seatType, $request, $body, $priceKey)
    {
        $ticketPrice = TicketPrice::where('screening_id', $showtime)
            ->where('seat_type', $seatType->value)
            ->first();

        if ($request->has($priceKey)) {
            if ($ticketPrice) {
                $ticketPrice->update([
                    'price' => $body[$priceKey]
                ]);
            } else {
                TicketPrice::create([
                    'screening_id' => $showtime,
                    'seat_type' => $seatType->value,
                    'price' => $body[$priceKey]
                ]);
            }
        } elseif ($ticketPrice) {
            $ticketPrice->delete();
        }
    }
}
