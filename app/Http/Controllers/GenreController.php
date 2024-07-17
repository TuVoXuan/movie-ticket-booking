<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class GenreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $search = $request->query('search');

            $genres = Genre::when($search, function ($query, $search) {
                $query->where('name', 'LIKE', '%' . $search . '%');
            })->get();

            return Inertia::render('Genres/Index', ['genres' => $genres]);
        } catch (\Throwable $th) {
            Log::error($th);
            return Inertia::render('Genres/Index')->with('error', 'An error occurred when get list genres.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = Validator::make($request->all(), [
                'name' => 'required|string|min:3'
            ]);
            if ($validated->fails()) {
                return back()->withErrors($validated->errors());
            }

            $body = $request->all();
            Genre::create([
                'name' => $body['name']
            ]);

            return redirect()->route('genres.index')->with('success', 'Create genre successfully.');
        } catch (\Throwable $th) {
            Log::error($th);
            return back()->with('error', 'An error occurred when creating genre.');
        }
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
        try {
            $body = $request->all();
            $validated = Validator::make($body, [
                'name' => 'required|string|min:3'
            ]);

            if ($validated->fails()) {
                return back()->withErrors($validated->errors());
            }

            $genre = Genre::find($id);
            if (!$genre) {
                return back()->with('error', 'Genre not found.');
            }

            $genre->update([
                'name' => $body['name']
            ]);
            return redirect()->route('genres.index')->with('success', 'Update genre successfully.');
        } catch (\Throwable $th) {
            Log::error($th);
            return back()->with('error', 'An error occurred when updating genre.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $genre = Genre::find($id);
            if (!$genre) {
                return redirect()->route('genres.index')->with('error', 'Genre not found');
            }
            $genre->delete();
            return redirect()->route('genres.index')->with('success', 'Delete genre successfully.');
        } catch (\Throwable $th) {
            Log::error($th);
            return redirect()->route('genres.index')->with('error', 'An error occurred when deleting genre.');
        }
    }
}
