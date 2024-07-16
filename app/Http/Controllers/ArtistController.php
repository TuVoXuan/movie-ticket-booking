<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class ArtistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Artist::query();

            $search = $request->query('search');
            $pageSize = $request->query('page_size', 10);
            $page = $request->query('page', 0);
            $sort = $request->query('sort');
            $sort_direction = $request->query('sort_direction', 'asc');

            if ($search) {
                $query->where('name', $search);
            }

            if ($sort) {
                $query->orderBy($sort, $sort_direction);
            }

            $artists = Artist::when($search, function ($query, $search) {
                $query->where('name', 'LIKE', '%' . $search . '%');
            })->when($sort, function ($query, $sort) use ($sort_direction) {
                $query->orderBy($sort, $sort_direction);
            })->paginate($pageSize);

            return Inertia::render('Artists/Index', ['artists' => $artists]);
        } catch (\Throwable $th) {
            Log::error($th);
            return Inertia::render('Artists/Index')->with('error', 'An error occurred when getting artists list');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Artists/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'name' => 'required|string|min:3',
            'biography' => 'required|string',
            'birthday' => 'required|date'
        ]);

        if ($validated->fails()) {
            return back()->withErrors($validated->errors());
        }

        try {
            $body = $request->all();
            Artist::create([
                'name' => $body['name'],
                'biography' => $body['biography'],
                'birthday' => Carbon::parse($body['birthday'])
            ]);
        } catch (\Throwable $th) {
            Log::error($th);
            return back()->withErrors(['error' => 'An error occurred during create artist']);
        }

        return redirect()->route('artists.index')->with('success', 'Create artist successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
