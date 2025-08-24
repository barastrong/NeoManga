<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\History;
use Illuminate\Http\Request;

class ApiChapterController extends Controller
{
    public function showApi(Request $request, Chapter $chapter)
    {
        $chapter->load('manga');

        if ($request->user()) {
            History::updateOrCreate(
                [
                    'user_id' => $request->user()->id,
                    'manga_id' => $chapter->manga_id,
                    'chapter_id' => $chapter->id,
                ],
                ['updated_at' => now()]
            );
        }
        
        $mangaChapters = $chapter->manga->chapters()
                              ->published()
                              ->orderBy('number', 'asc')
                              ->get(['id', 'slug', 'number']);

        $currentChapterIndex = $mangaChapters->search(fn($c) => $c->id === $chapter->id);

        $nextChapter = $mangaChapters->get($currentChapterIndex + 1);
        $prevChapter = $mangaChapters->get($currentChapterIndex - 1);
        
        return response()->json([
            'chapter' => $chapter,
            'prev_chapter' => $prevChapter,
            'next_chapter' => $nextChapter,
            'all_chapters' => $mangaChapters,
        ]);
    }
}