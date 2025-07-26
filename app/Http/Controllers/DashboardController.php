<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Manga;
use App\Models\History;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index() 
    {
        $popularMangaIds = History::query()
            ->select('manga_id')
            ->selectRaw('COUNT(DISTINCT user_id) as unique_readers_count')
            ->groupBy('manga_id')
            ->having('unique_readers_count', '>', 1)
            ->orderByDesc('unique_readers_count')
            ->take(12)
            ->pluck('manga_id');

        $popularMangas = collect();
        
        if ($popularMangaIds->isNotEmpty()) {
            $popularMangas = Manga::with('latestPublishedChapter')
                ->withAvg('ratings', 'rating')
                ->withCount('histories')
                ->whereIn('id', $popularMangaIds)
                ->orderByRaw(DB::raw("FIELD(id, " . $popularMangaIds->implode(',') . ")"))
                ->get();
        }
        
        $mangas = Manga::with('genres', 'latestPublishedChapter')
                       ->withAvg('ratings', 'rating')
                       ->latest()
                       ->paginate(18);

        return view('dashboard', compact('mangas', 'popularMangas'));
    }
}