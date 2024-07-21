<?php

namespace App\Http\Controllers\API;

use App\Models\Genre;
use Illuminate\Http\Request;

class GenreController extends BaseController
{
    public function getAll()
    {
        return $this->sendResponse(Genre::all()->select(['id', 'name']), 'Get all genres successfully.');
    }
}
