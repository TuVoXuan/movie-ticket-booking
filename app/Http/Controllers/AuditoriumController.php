<?php

namespace App\Http\Controllers;

use App\Enums\SeatType;
use App\Helpers\SlugHelper;
use App\Models\Auditorium;
use App\Models\CinemaBranch;
use App\Models\SeatingArrangement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class AuditoriumController extends Controller
{
    public function index(Request $request, string $branch)
    {
        $cinemaBranch = CinemaBranch::where('code', '=', $branch)->first();

        if (!$cinemaBranch) {
            return Inertia::render('Cinemas/Auditoria/Index', ['auditoria' => null])->with('error', 'Cinema branch not found');
        }

        $auditoria = Auditorium::where('cinema_branch_id', '=', $cinemaBranch->id)->get();

        return Inertia::render('Cinemas/Auditoria/Index', ['auditoria' => $auditoria]);
    }

    public function create()
    {
        return Inertia::render('Cinemas/Auditoria/CreateAndUpdate');
    }

    public function store(Request $request, string $branch)
    {
        try {
            $body = $request->all();
            $validated = Validator::make($body, [
                'name' => 'required|string|min:1',
                'capacity' => 'required|numeric|min:0',
                'seat_direction' => 'required|string',
                'rows' => 'required|numeric|min:0',
                'columns' => 'required|numeric|min:0',
                'grid_layout' => 'required|array',
                'grid_layout.*' => 'required|array',
                'grid_layout.*.*.seatLabel' => 'nullable|string',
                'grid_layout.*.*.type' => ['required', Rule::enum(SeatType::class)]
            ]);

            if ($validated->fails()) {
                return back()->withErrors($validated->errors());
            }

            $cinemaBranch = CinemaBranch::where('code', $branch)->first();

            $code = SlugHelper::convertToSlug($body['name']);
            $auditorium = Auditorium::create([
                'name' => $body['name'],
                'cinema_branch_id' => $cinemaBranch->id,
                'capacity' => $body['capacity'],
                'seat_direction' => $body['seat_direction'],
                'rows' => $body['rows'],
                'columns' => $body['columns'],
                'code' => $code
            ]);

            $gridLayout = $body['grid_layout'];
            foreach ($gridLayout as $rowKey => $rowVal) {
                foreach ($rowVal as $colKey => $colValue) {
                    if ($colValue['type'] !== SeatType::Unset->value) {
                        SeatingArrangement::create([
                            'auditorium_id' => $auditorium->id,
                            'label' => $colValue['seatLabel'],
                            'seat_type' => $colValue['type'],
                            'x_position' => $colKey,
                            'y_position' => $rowKey
                        ]);
                    }
                }
            }

            return redirect()->route('cinemas.branches.auditoria.index', ['branch' => $branch])->with('success', 'Create auditorium successfully.');
        } catch (\Throwable $th) {
            Log::error($th);
            return redirect()->route('cinemas.branches.auditoria.create', ['branch' => $branch])->with('error', 'An error occurred during store auditorium.');
        }
    }

    public function edit(Request $request, string $branch, string $auditorium)
    {
        try {
            $auditorium = Auditorium::whereHas('cinemaBranch', function ($query) use ($branch) {
                $query->where('code', '=', $branch);
            })->where('code', '=', $auditorium)->first();


            $seatingArrangements = SeatingArrangement::where('auditorium_id', '=', $auditorium->id)
                ->get()->groupBy('y_position')
                ->map(function ($group) {
                    return $group->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'label' => $item->label,
                            'seat_type' => $item->seat_type,
                            'x_position' => $item->x_position,
                        ];
                    });
                });

            $auditoriumArray = $auditorium->toArray();
            $auditoriumArray['seating_arrangements'] = $seatingArrangements;
            return Inertia::render('Cinemas/Auditoria/CreateAndUpdate', ['auditorium' => $auditoriumArray]);
        } catch (\Throwable $th) {
            Log::error($th);
            return redirect()->route('cinemas.branches.auditoria.index', ['branch' => $branch])->with('error', 'An error occurred during get auditorium.');
        }
    }
}
