<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Manga;

class DashboardController extends Controller
{
    public function index() 
    {
        $mangas = Manga::with('genres')
                       ->withAvg('ratings', 'rating')
                       ->latest()
                       ->paginate(16);

        return view('dashboard', compact('mangas'));
    }
}