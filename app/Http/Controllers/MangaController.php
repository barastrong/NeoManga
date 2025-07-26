<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Manga;
use App\Models\Chapter;
use App\Models\History;
use Illuminate\Support\Facades\Auth;
use App\Models\Genre;

class MangaController extends Controller
{
    public function mangaList(Request $request)
    {
        $query = Manga::query();

        if ($request->filled('genre') && is_array($request->genre)) {
            $query->whereHas('genres', function ($q) use ($request) {
                $q->whereIn('genres.id', $request->genre);
            });
        }

        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('type') && $request->type != 'all') {
            $query->where('type', $request->type);
        }

        $order = $request->input('order', 'latest');
        switch ($order) {
            case 'popularity':
                $query->withCount('bookmarks')->orderBy('bookmarks_count', 'desc');
                break;
            case 'rating':
                $query->withAvg('ratings', 'rating')->orderBy('ratings_avg_rating', 'desc');
                break;
            default:
                $query->latest('updated_at');
                break;
        }

        $mangas = $query->with('latestPublishedChapter')
                        ->withAvg('ratings', 'rating')
                        ->paginate(30) // Lebih banyak item per halaman
                        ->withQueryString();

        $genres = Genre::orderBy('name')->get();

        return view('manga.list', compact('mangas', 'genres'));
    }

    public function show($slug)
    {
        $manga = Manga::where('slug', $slug)
                      ->with(['genres', 'user', 'bookmarks', 'comments.user', 'comments.replies.user'])
                      ->firstOrFail();
        
        $chapters = Chapter::where('manga_id', $manga->id)
                          ->published()
                          ->orderBy('number', 'asc')
                          ->get();
        
        $isBookmarked = false;
        $readChapters = [];
        $userHistories = collect();

        if (Auth::check()) {
            $user = Auth::user();
            $isBookmarked = $manga->isBookmarkedBy($user->id);
            
            $readChapters = History::where('user_id', $user->id)
                                  ->where('manga_id', $manga->id)
                                  ->pluck('chapter_id')
                                  ->toArray();

            $userHistories = History::with('chapter')
                ->where('user_id', $user->id)
                ->where('manga_id', $manga->id)
                ->where('updated_at', '>=', now()->subDays(5))
                ->latest('updated_at')
                ->take(10) 
                ->get();
            
        }
        
        return view('manga.show', compact('manga', 'chapters', 'isBookmarked', 'readChapters', 'userHistories'));
    }
}