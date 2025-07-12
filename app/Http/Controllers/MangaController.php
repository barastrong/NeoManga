<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Manga;
use App\Models\Chapter;
use App\Models\History;
use Illuminate\Support\Facades\Auth;

class MangaController extends Controller
{
    public function index()
    {
        $mangas = Manga::with(['genres', 'user'])
                       ->orderBy('created_at', 'desc')
                       ->paginate(12);
        
        return view('manga.index', compact('mangas'));
    }

    public function show($slug)
    {
        $manga = Manga::where('slug', $slug)
                      ->with(['genres', 'user', 'bookmarks'])
                      ->firstOrFail();
        
        $chapters = Chapter::where('manga_id', $manga->id)
                          ->published()
                          ->orderBy('number', 'asc')
                          ->get();
        
        $isBookmarked = false;
        $readChapters = [];
        
        if (Auth::check()) {
            $isBookmarked = $manga->isBookmarkedBy(Auth::id());
            
            $readChapters = History::where('user_id', Auth::id())
                                  ->where('manga_id', $manga->id)
                                  ->pluck('chapter_id')
                                  ->toArray();
        }
        
        return view('manga.show', compact('manga', 'chapters', 'isBookmarked', 'readChapters'));
    }
}