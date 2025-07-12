<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bookmark;
use App\Models\Manga;
use Illuminate\Support\Facades\Auth;

class BookmarkController extends Controller
{
    /**
     * Toggle bookmark untuk manga
     */
    public function toggle(Request $request, $mangaId)
    {
        // Pastikan user sudah login
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'You must be logged in to bookmark manga'
            ], 401);
        }

        $manga = Manga::findOrFail($mangaId);
        $userId = Auth::id();

        // Cek apakah sudah ada bookmark
        $existingBookmark = Bookmark::where('user_id', $userId)
                                   ->where('manga_id', $mangaId)
                                   ->first();

        if ($existingBookmark) {
            // Jika sudah ada, hapus bookmark
            $existingBookmark->delete();
            $isBookmarked = false;
            $message = 'Bookmark removed successfully';
        } else {
            // Jika belum ada, buat bookmark baru
            Bookmark::create([
                'user_id' => $userId,
                'manga_id' => $mangaId,
                'bookmark' => true
            ]);
            $isBookmarked = true;
            $message = 'Bookmark added successfully';
        }

        // Hitung ulang jumlah followers
        $followersCount = $manga->bookmarks()->count();

        return response()->json([
            'success' => true,
            'message' => $message,
            'is_bookmarked' => $isBookmarked,
            'followers_count' => $followersCount
        ]);
    }

    /**
     * Tampilkan daftar bookmark user
     */
    public function index()
    {
        $bookmarks = Bookmark::where('user_id', Auth::id())
                            ->with(['manga.genres', 'manga.user'])
                            ->orderBy('created_at', 'desc')
                            ->paginate(12);

        return view('bookmark.index', compact('bookmarks'));
    }
}