<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Manga;
use App\Models\Chapter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ChapterImportController extends Controller
{
    /**
     * Menyimpan chapter baru yang diimpor dari bot.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'manga_slug' => 'required|string|max:255',
            'chapter_number' => 'required|string|max:50',
            'images' => 'required|array',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $manga = Manga::where('slug', $request->manga_slug)->first();

            if (!$manga) {
                // Sesuai logika, bot tidak akan mengirim chapter jika manga tidak ada.
                // Tapi sebagai pengaman, kita tetap berikan respons 404.
                return response()->json(['message' => 'Manga not found'], 404);
            }

            if (Chapter::where('manga_id', $manga->id)->where('number', $request->chapter_number)->exists()) {
                return response()->json(['message' => 'Chapter ' . $request->chapter_number . ' already exists.'], 200);
            }

            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store(
                    'chapters/' . $manga->slug . '/' . $request->chapter_number,
                    'public'
                );
                $imagePaths[] = $path;
            }

            Chapter::create([
                'manga_id' => $manga->id,
                'number' => $request->chapter_number,
                'slug' => Str::random(10), // Slug unik untuk setiap chapter
                'chapter_images' => $imagePaths,
                'status' => 'published',
            ]);
            
            return response()->json([
                'message' => 'Chapter ' . $request->chapter_number . ' imported successfully for manga: ' . $manga->title,
            ], 201);

        } catch (\Exception $e) {
            // Log error untuk debugging
            // \Log::error('Chapter Import Failed: ' . $e->getMessage());
            return response()->json(['message' => 'An server error occurred.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Mengecek keberadaan manga dan mengembalikan nomor chapter terakhir yang dimiliki.
     * Logika ini penting untuk bot Python.
     *
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkMangaExists($slug)
    {
        $manga = Manga::where('slug', $slug)->with('chapters')->first();

        if (!$manga) {
            return response()->json([
                'exists' => false,
                'latest_chapter' => null
            ]);
        }
        
        // Sesuaikan dengan struktur tabel Anda:
        // Gunakan 'status' dan 'published' sesuai method store()
        $latestChapterNumber = $manga->chapters()
                                     ->where('status', 'published') 
                                     ->max('number');

        return response()->json([
            'exists' => true,
            'latest_chapter' => $latestChapterNumber
        ]);
    }
}