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

        return Inertia::render('Cinemas/Auditoria/Index', ['auditoria' => $auditoria, 'cinemaBranchName' => $cinemaBranch->name]);
    }

    public function create(Request $request, string $branch)
    {
        $cinemaBranch = CinemaBranch::where('code', '=', $branch)->first();

        return Inertia::render('Cinemas/Auditoria/CreateAndUpdate', ['cinemaBranchName' => $cinemaBranch->name]);
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
            $cinemaBranch = CinemaBranch::where('code', '=', $branch)->first();

            return Inertia::render('Cinemas/Auditoria/CreateAndUpdate', ['auditorium' => $auditoriumArray, 'cinemaBranchName' => $cinemaBranch->name]);
        } catch (\Throwable $th) {
            Log::error($th);
            return redirect()->route('cinemas.branches.auditoria.index', ['branch' => $branch])->with('error', 'An error occurred during get auditorium.');
        }
    }

    public function update(Request $request, string $branch, string $auditorium)
    {
        try {
            $body = $request->all();
            $validated = Validator::make($body, [
                'name' => 'required|string|min:1',
                'capacity' => 'required|numeric|min:0',
                'seat_direction' => 'required|string',
                'rows' => 'required|numeric|min:0',
                'columns' => 'required|numeric|min:0',
                'added_cells' => 'sometimes|required|array',
                'added_cells.*.seatLabel' => 'sometimes|nullable|string',
                'added_cells.*.x_position' => 'sometimes|required|numeric',
                'added_cells.*.y_position' => 'sometimes|required|string',
                'added_cells.*.type' => ['sometimes', 'required', Rule::enum(SeatType::class)],
                'deleted_cells' => 'sometimes|required|array',
                'deleted_cells.*.id' => 'sometimes|required|numeric',
                'updated_cells' => 'sometimes|required|array',
                'updated_cells.*.id' => 'sometimes|required|numeric',
                'updated_cells.*.seatLabel' => 'sometimes|nullable|string',
                'updated_cells.*.type' => ['sometimes', 'required', Rule::enum(SeatType::class)],
                'updated_cells.*.x_position' => 'sometimes|required|numeric',
                'updated_cells.*.y_position' => 'sometimes|required|string',
            ]);

            if ($validated->fails()) {
                return back()->withErrors($validated->errors());
            }

            $auditoriumModel = Auditorium::whereHas('cinemaBranch', function ($query) use ($branch) {
                $query->where('code', '=', $branch);
            })->where('code', '=', $auditorium)->first();

            if (!$auditoriumModel) {
                return back()->with('error', 'Auditorium not found.');
            }

            $code = SlugHelper::convertToSlug($body['name']);
            $auditoriumModel->update([
                'name' => $body['name'],
                'capacity' => $body['capacity'],
                'seat_direction' => $body['seat_direction'],
                'rows' => $body['rows'],
                'columns' => $body['columns'],
                'code' => $code
            ]);

            if ($request->has('added_cells')) {
                foreach ($body['added_cells'] as $cell) {
                    SeatingArrangement::create([
                        'auditorium_id' => $auditoriumModel->id,
                        'label' => $cell['seatLabel'],
                        'seat_type' => $cell['type'],
                        'x_position' => $cell['x_position'],
                        'y_position' => $cell['y_position']
                    ]);
                }
            }

            if ($request->has('updated_cells')) {
                foreach ($body['updated_cells'] as $cell) {
                    $seat = SeatingArrangement::find($cell['id']);
                    if (!$seat) {
                        return back()->with('error', 'Seat not found to update.');
                    }

                    $seat->update([
                        'label' => $cell['seatLabel'],
                        'seat_type' => $cell['type'],
                        'x_position' => $cell['x_position'],
                        'y_position' => $cell['y_position']
                    ]);
                }
            }

            if ($request->has('deleted_cells')) {
                foreach ($body['deleted_cells'] as $cell) {
                    SeatingArrangement::destroy($cell['id']);
                }
            }

            return redirect()->route('cinemas.branches.auditoria.index', ['branch' => $branch])->with('success', 'Update auditorium successfully.');
        } catch (\Throwable $th) {
            Log::error($th);
            return redirect()->route('cinemas.branches.auditoria.edit', ['branch' => $branch, 'auditorium' => $auditorium])->with('error', 'An error occurred during update auditorium.');
        }
    }
}
