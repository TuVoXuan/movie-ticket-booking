<?php

namespace App\Http\Controllers;

use App\Helpers\SlugHelper;
use App\Models\CinemaBranch;
use App\Models\CinemaCompany;
use App\Models\File;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class CinemaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function companies()
    {
        return Inertia::render('Cinemas/Companies', ['cinemas' => CinemaCompany::with('logo')->get()]);
    }

    public function branches(Request $request)
    {
        try {
            $search = $request->query('search');
            $pageSize = $request->query('page_size', 10);
            $sort = $request->query('sort');
            $sort_order = $request->query('sort_order', 'asc');
            $region = $request->query('region');
            $company = $request->query('company');

            $cinemaBranches = CinemaBranch::with(['region', 'cinemaCompany.logo'])
                ->when($search, function ($query, $search) {
                    $query->where('name', 'LIKE', '%' . $search . '%');
                })
                ->when($sort, function ($query, $sort) use ($sort_order) {
                    $query->orderBy($sort, $sort_order);
                })
                ->when($region, function ($query) use ($region) {
                    $query->whereHas('region', function ($q) use ($region) {
                        $q->where('code', '=', $region);
                    });
                })
                ->when($company, function ($query) use ($company) {
                    $query->whereHas('cinemaCompany', function ($q) use ($company) {
                        $q->where('code', '=', $company);
                    });
                })
                ->paginate($pageSize);


            return Inertia::render('Cinemas/Branches/Index', ['cinemaBranches' => $cinemaBranches]);
        } catch (\Throwable $th) {
            Log::error($th);
            return Inertia::render('Cinemas/Branches/Index')->with('error', 'An error occurred during get list cinema branches.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createBranch()
    {
        return Inertia::render('Cinemas/Branches/CreateAndUpdate');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeCinema(Request $request)
    {
        try {
            $body = $request->all();
            $validated = Validator::make($body, [
                'name' => 'required|min:5',
                'logo' => 'required|image'
            ]);

            if ($validated->fails()) {
                return back()->withErrors($validated->errors());
            }

            $logoResult = $request->file('logo')->storeOnCloudinary('movie_ticket_booking');
            $logoURL = $logoResult->getSecurePath();
            $thumbnailPublicId = $logoResult->getPublicId();

            $file = File::create([
                'public_id' => $thumbnailPublicId,
                'url' => $logoURL
            ]);

            $code = SlugHelper::convertToSlug($body['name']);
            CinemaCompany::create([
                'name' => $body['name'],
                'logo' => $file->id,
                'code' => $code
            ]);

            return redirect()->route('cinemas.companies.index')->with('success', 'Create cinema successfully.');
        } catch (\Throwable $th) {
            Log::error($th);
            return redirect()->route('cinemas.companies.index')->with('error', 'An error occurred during create cinema');
        }
    }

    public function updateCinema(Request $request, string $id)
    {
        try {
            $cinemaCompany = CinemaCompany::find($id);
            if (!$cinemaCompany) {
                return back()->with('error', 'Cinema not found.');
            }

            $body = $request->all();
            $validated = Validator::make($body, [
                'name' => 'required|string|min:3',
                'logo' => 'sometimes|image'
            ]);

            if ($validated->failed()) {
                return back()->withErrors($validated->errors());
            }

            if ($request->has('logo')) {
                $oldLogo = File::find($cinemaCompany->logo);

                $logoResult = $request->file('logo')->storeOnCloudinary('movie_ticket_booking');
                $logoURL = $logoResult->getSecurePath();
                $thumbnailPublicId = $logoResult->getPublicId();

                $file = File::create([
                    'public_id' => $thumbnailPublicId,
                    'url' => $logoURL
                ]);
                $cinemaCompany->update([
                    'logo' => $file->id
                ]);

                if ($oldLogo) {
                    Cloudinary::destroy($oldLogo->public_id);
                    $oldLogo->delete();
                }
            }
            $code = SlugHelper::convertToSlug($body['name']);
            $cinemaCompany->update([
                'name' => $body['name'],
                'code' => $code
            ]);

            return redirect()->route('cinemas.companies.index')->with('success', 'Update cinema successfully.');
        } catch (\Throwable $th) {
            Log::error($th);
            return redirect()->route('cinemas.companies.index')->with('error', 'An error occurred during update cinema');
        }
    }

    public function storeBranch(Request $request)
    {
        try {
            $body = $request->all();
            $validated = Validator::make($body, [
                'name' => 'required|min:10',
                'address' => 'required|min:10',
                'region' => 'required|numeric|exists:regions,id',
                'company' => 'required|numeric|exists:cinema_companies,id'
            ]);

            if ($validated->fails()) {
                return back()->withErrors($validated->errors());
            }

            $code = SlugHelper::convertToSlug($body['name']);
            CinemaBranch::create([
                'name' => $body['name'],
                'address' => $body['address'],
                'region_id' => $body['region'],
                'cinema_company_id' => $body['company'],
                'code' => $code,
                'latitude' => 0,
                'longitude' => 0
            ]);

            return redirect()->route('cinemas.branches.index')->with('success', 'Create cinema branch successfully.');
        } catch (\Throwable $th) {
            Log::error($th);
            return redirect()->route('cinemas.branches.index')->with('error', 'An error occurred during create branch');
        }
    }

    public function editBranch(Request $request, string $branch)
    {
        try {
            $cinemaBranch = CinemaBranch::with(['region', 'cinemaCompany'])->where('code', '=', $branch)->first();
            if (!$cinemaBranch) {
                return redirect()->route('cinemas.branches.index')->with('error', 'Cinema Branch not found.');
            }

            return Inertia::render('Cinemas/Branches/CreateAndUpdate', ['cinemaBranch' => $cinemaBranch]);
        } catch (\Throwable $th) {
            Log::error($th);
            return redirect()->route('cinemas.branches.index')->with('error', 'An error occurred during get cinema branch.');
        }
    }

    public function updateBranch(Request $request, string $id)
    {
        try {
            $body = $request->all();
            Log::info($body);
            $validated = Validator::make($body, [
                'name' => 'required|min:10',
                'address' => 'required|min:10',
                'region' => 'required|numeric|exists:regions,id',
                'company' => 'required|numeric|exists:cinema_companies,id'
            ]);

            if ($validated->fails()) {
                return back()->withErrors($validated->errors());
            }

            $branch = CinemaBranch::find($id);
            if (!$branch) {
                return back()->with('error', 'Cinema Branch not found.');
            }

            $branch->update([
                'name' => $body['name'],
                'address' => $body['address'],
                'region_id' => $body['region'],
                'cinema_company_id' => $body['company'],
                'code' => SlugHelper::convertToSlug($body['name'])
            ]);

            return redirect()->route('cinemas.branches.index')->with('success', 'Update cinema branch successfully.');
        } catch (\Throwable $th) {
            Log::error($th);
            return back()->with('error', 'An error occurred during update cinema branch.');
        }
    }

    public function destroyCinema(string $id)
    {
        try {
            $cinema = CinemaCompany::find($id);
            if (!$cinema) {
                return redirect()->route('cinemas.companies.index')->with('error', 'Cinema not found.');
            }

            $logo = File::find($cinema->logo);
            $cinema->delete();
            Cloudinary::destroy($logo->public_id);
            $logo->delete();

            return redirect()->route('cinemas.companies.index')->with('success', 'Delete cinema successfully.');
        } catch (\Throwable $th) {
            Log::error($th);
            return redirect()->route('cinemas.companies.index')->with('error', 'An error occurred during delete cinema.');
        }
    }
}
