<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Manga;
use App\Models\Chapter;
use App\Models\History;
use Illuminate\Support\Facades\Auth;

class MangaController extends Controller
{
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