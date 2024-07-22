<?php

namespace App\Http\Controllers;

use App\Helpers\SlugHelper;
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
            $search = $request->query('search');
            $pageSize = $request->query('page_size', 10);
            $sort = $request->query('sort');
            $sort_order = $request->query('sort_order', 'asc');

            $artists = Artist::when($search, function ($query, $search) {
                $query->where('name', 'LIKE', '%' . $search . '%');
            })->when($sort, function ($query, $sort) use ($sort_order) {
                $query->orderBy($sort, $sort_order);
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
        return Inertia::render('Artists/CreateAndUpdate');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'name' => 'required|string|min:3',
            'biography' => 'string',
            'birthday' => 'date|nullable'
        ]);

        if ($validated->fails()) {
            return back()->withErrors($validated->errors());
        }

        try {
            $body = $request->all();
            $code = SlugHelper::convertToSlug($body['name']);
            Artist::create([
                'name' => $body['name'],
                'biography' => isset($body['birthday']) ? $body['biography'] : null,
                'birthday' => isset($body['birthday']) ? Carbon::parse($body['birthday']) : null,
                'code' => $code
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
    public function edit(Artist $artist)
    {
        return Inertia::render('Artists/CreateAndUpdate', ['artist' => $artist]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(string $id, Request $request)
    {
        try {
            $artist = Artist::find($id);
            if (!$artist) {
                return back()->with('error', 'Artist not found.');
            }

            $validated = Validator::make($request->all(), [
                'name' => 'required|string|min:3',
                'biography' => 'required|string',
                'birthday' => 'required|date'
            ]);

            if ($validated->fails()) {
                return back()->withErrors($validated->errors());
            }
            $body = $request->all();
            $artist->update([
                'name' => $body['name'],
                'biography' => $body['biography'],
                'birthday' => Carbon::parse($body['birthday'])
            ]);
            return redirect()->route('artists.index')->with('success', 'Update artist successfully.');
        } catch (\Throwable $th) {
            Log::error($th);
            return redirect()->route('artists.index')->with('error', 'An error occurred when updating artist.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $artist = Artist::find($id);
            if (!$artist) {
                return redirect()->route('artists.index')->with('error', 'Artist not found.');
            }
            $artist->delete();
            return redirect()->route('artists.index')->with('success', 'Delete artists successfully.');
        } catch (\Throwable $th) {
            return redirect()->route('artists.index')->with('error', 'An error occurred when delete artist.');
        }
    }
}
