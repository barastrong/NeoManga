<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chapter;
use App\Models\Manga;

class ChapterController extends Controller
{
    public function show($slug)
    {
        $chapter = Chapter::where('slug', $slug)
                          ->with('manga')
                          ->published()
                          ->firstOrFail();
        
        // Get next and previous chapters
        $nextChapter = Chapter::where('manga_id', $chapter->manga_id)
                             ->where('number', '>', $chapter->number)
                             ->published()
                             ->orderBy('number', 'asc')
                             ->first();
        
        $prevChapter = Chapter::where('manga_id', $chapter->manga_id)
                             ->where('number', '<', $chapter->number)
                             ->published()
                             ->orderBy('number', 'desc')
                             ->first();
        
        return view('chapter.show', compact('chapter', 'nextChapter', 'prevChapter'));
    }
}