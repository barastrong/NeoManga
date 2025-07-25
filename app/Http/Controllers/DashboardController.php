<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Manga;
use App\Models\History;

class DashboardController extends Controller
{
    public function index() 
    {
        $popularMangaIds = History::query()
            ->select('manga_id')
            ->groupBy('manga_id')
            ->havingRaw('COUNT(manga_id) > 5')
            ->pluck('manga_id');

        $popularMangas = Manga::with('latestPublishedChapter')
            ->withAvg('ratings', 'rating')
            ->withCount('histories')
            ->whereIn('id', $popularMangaIds)
            ->orderByDesc('histories_count')
            ->take(12)
            ->get();
        
        $mangas = Manga::with('genres', 'latestPublishedChapter')
                       ->withAvg('ratings', 'rating')
                       ->latest()
                       ->paginate(18);

        return view('dashboard', compact('mangas', 'popularMangas'));
    }
}