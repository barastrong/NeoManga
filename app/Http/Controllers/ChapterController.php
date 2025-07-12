<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chapter;
use App\Models\Manga;
use App\Models\History;
use Illuminate\Support\Facades\Auth;

class ChapterController extends Controller
{
    public function show($slug)
    {
        $chapter = Chapter::where('slug', $slug)
                          ->with('manga')
                          ->published()
                          ->firstOrFail();
        
        if (Auth::check()) {
            History::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'manga_id' => $chapter->manga_id,
                    'chapter_id' => $chapter->id,
                ],
                [
                    'updated_at' => now()
                ]
            );
        }
        
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