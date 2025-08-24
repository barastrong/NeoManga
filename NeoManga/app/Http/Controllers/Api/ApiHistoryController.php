<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\History;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiHistoryController extends Controller
{
    /**
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = $request->user(); 

        $latestHistorySubquery = History::selectRaw('MAX(id) as id')
            ->where('user_id', $user->id)
            ->groupBy('manga_id');

        $histories = History::with([
                'manga' => function ($query) {
                    $query->withAvg('ratings', 'rating');
                },
                'chapter'
            ])
            ->whereIn('id', $latestHistorySubquery)
            ->latest('id')
            ->paginate(18); // Pagination untuk API

        return response()->json($histories);
    }

    /**
     *
     * @param  \App\Models\History  $history
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, History $history)
    {
        if ($history->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $history->delete();

        return response()->json(['message' => 'Riwayat berhasil dihapus.']);
    }

    /**
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearAll(Request $request)
    {
        History::where('user_id', $request->user()->id)->delete();

        return response()->json(['message' => 'Semua riwayat telah berhasil dibersihkan.']);
    }

    /**
     *
     * @param  int  $mangaId
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearForManga(Request $request, $mangaId)
    {
        History::where('user_id', $request->user()->id)
               ->where('manga_id', $mangaId)
               ->delete();

        return response()->json(['message' => 'Riwayat untuk manga ini telah direset.']);
    }
}