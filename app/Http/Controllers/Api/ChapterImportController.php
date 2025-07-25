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
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'manga_slug' => 'required|string|max:255',
            'chapter_number' => 'required|string|max:50', // Tetap string untuk menampung angka seperti '10.5'
            'images' => 'required|array',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Mencari manga berdasarkan slug (ini sudah benar)
            $manga = Manga::where('slug', $request->manga_slug)->first();

            if (!$manga) {
                // Logika manga tidak ditemukan (ini sudah benar)
                $this->logMissingManga($request->manga_slug);
                return response()->json(['message' => 'Manga not found'], 404);
            }

            // Cek chapter duplikat (ini sudah benar)
            if (Chapter::where('manga_id', $manga->id)->where('number', $request->chapter_number)->exists()) {
                return response()->json(['message' => 'Chapter already exists.'], 200);
            }

            // Simpan gambar-gambar chapter
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                // === PERBAIKAN NAMA FOLDER ===
                // Path: chapters/manga-slug/nomor-chapter-bersih/image.jpg
                $path = $image->store(
                    'chapters/' . $manga->slug . '/' . $request->chapter_number,
                    'public'
                );
                $imagePaths[] = $path;
            }

            // Buat entri chapter baru di database
            Chapter::create([
                'manga_id' => $manga->id, // manga_id ini sekarang pasti benar
                'number' => $request->chapter_number, // Nomor chapter bersih
                
                'slug' => Str::random(10),
                
                'chapter_images' => $imagePaths,
                
                // === PERBAIKAN STATUS ===
                'status' => 'published',
            ]);
            
            return response()->json([
                'message' => 'Chapter ' . $request->chapter_number . ' imported successfully for manga: ' . $manga->title,
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['message' => 'An server error occurred.', 'error' => $e->getMessage()], 500);
        }
    }

    // Fungsi logMissingManga tetap sama
    private function logMissingManga($slug) { /* ... */ }
}