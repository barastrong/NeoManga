<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Manga;
use App\Models\Chapter;

class DashboardController extends Controller
{
    public function index() 
    {
        $mangas = Manga::with('genres', 'latestPublishedChapter')
                       ->withAvg('ratings', 'rating')
                       ->latest()
                       ->paginate(16);
        return view('dashboard', compact('mangas'));
    }
}