<?php

namespace App\Http\Controllers;

use App\Enums\FilmTranslation;
use App\Models\Auditorium;
use App\Models\Screening;
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
                })->when($screening_date, function ($query) use ($screening_date) {
                    $date = new DateTime($screening_date);
                    $startDate = $date->setTime(0, 0, 0)->format('Y-m-d H:i:s');
                    $endDate = $date->setTime(23, 59, 59)->format('Y-m-d H:i:s');

                    $query->whereBetween('screening_time', [Carbon::parse($startDate), Carbon::parse($endDate)]);
                })->paginate($pageSize);

            return Inertia::render('Cinemas/Showtimes/Index', ['showtimes' => $showtimes]);
        } catch (\Throwable $th) {
            Log::error($th);
            return redirect()->route('cinemas.branches.showtimes.index', ['branch' => $branch])->with('error', 'An error occurred during get list showtimes.');
        }
    }

    public function create()
    {
        return Inertia::render('Cinemas/Showtimes/CreateAndUpdate');
    }

    public function store(Request $request, string $branch)
    {
        try {
            $body = $request->all();
            $validated = Validator::make($body, [
                'film' => 'required|numeric|exists:films,id',
                'auditorium' => 'required|numeric|exists:auditoria,id',
                'screening_time' => 'required|date',
                'film_translation' => ['required', Rule::enum(FilmTranslation::class)]
            ]);

            if ($validated->fails()) {
                return back()->withErrors($validated->errors());
            }

            Screening::create([
                'film_id' => $body['film'],
                'auditorium_id' => $body['auditorium'],
                'screening_time' => Carbon::parse($body['screening_time']),
                'film_translation' => $body['film_translation']
            ]);

            return redirect()->route('cinemas.branches.showtimes.index', ['branch' => $branch])->with('success', 'Create new screening successfully.');
        } catch (\Throwable $th) {
            Log::error($th);
            return back()->with('error', 'And error occurred during create screening.');
        }
    }
}
