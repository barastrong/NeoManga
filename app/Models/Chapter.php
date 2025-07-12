<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Chapter extends Model
{
    use HasFactory;

    protected $fillable = [
        'manga_id',
        'slug',
        'number',
        'status',
        'chapter_images',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'chapter_images' => 'array',
    ];

    /**
     * Boot method untuk auto-generate slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($chapter) {
            if (empty($chapter->slug) && $chapter->manga && $chapter->number) {
                $chapter->slug = Str::slug($chapter->manga->title . '-chapter-' . $chapter->number);
            }
        });
    }

    /**
     * Relasi: Chapter milik satu Manga
     */
    public function manga()
    {
        return $this->belongsTo(Manga::class);
    }

    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk published chapters
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope untuk draft chapters
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Helper method untuk mendapatkan chapter_images sebagai array
     */
    private function getChapterImagesArray()
    {
        $images = $this->chapter_images;
        
        // Jika masih berupa string JSON, decode dulu
        if (is_string($images)) {
            $images = json_decode($images, true);
        }
        
        // Pastikan return array
        return is_array($images) ? $images : [];
    }

    /**
     * Accessor untuk mendapatkan cover image dari chapter_images
     */
    public function getCoverImageUrlAttribute()
    {
        $images = $this->getChapterImagesArray();

        // Jika chapter_images adalah array dan tidak kosong
        if (!empty($images)) {
            // Pastikan path sudah benar dengan storage
            $imagePath = $images[0];
            
            // Cek apakah path sudah mengandung 'storage/' atau belum
            if (str_starts_with($imagePath, 'storage/')) {
                return asset($imagePath);
            } else {
                return asset('storage/' . $imagePath);
            }
        }

        return asset('images/no-image.png');
    }

    /**
     * Accessor untuk mendapatkan semua image URLs
     */
    public function getImageUrlsAttribute()
    {
        $images = $this->getChapterImagesArray();

        if (!empty($images)) {
            return array_map(function ($image) {
                // Cek apakah path sudah mengandung 'storage/' atau belum
                if (str_starts_with($image, 'storage/')) {
                    return asset($image);
                } else {
                    return asset('storage/' . $image);
                }
            }, $images);
        }

        return [];
    }

    /**
     * Accessor untuk mendapatkan jumlah gambar
     */
    public function getImageCountAttribute()
    {
        $images = $this->getChapterImagesArray();
        return count($images);
    }

    /**
     * Accessor untuk status yang lebih readable
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'draft' => 'Draft',
            'published' => 'Published',
            'fixed' => 'Fixed',
            default => 'Not Set'
        };
    }

    /**
     * Method untuk mendapatkan first image untuk preview
     */
    public function getFirstImageAttribute()
    {
        $images = $this->getChapterImagesArray();
        
        if (!empty($images)) {
            return $images[0];
        }
        
        return null;
    }

    /**
     * Accessor untuk mendapatkan first image URL untuk preview
     */
    public function getFirstImageUrlAttribute()
    {
        $images = $this->getChapterImagesArray();
        
        if (!empty($images)) {
            $imagePath = $images[0];
            
            // Cek apakah path sudah mengandung 'storage/' atau belum
            if (str_starts_with($imagePath, 'storage/')) {
                return asset($imagePath);
            } else {
                return asset('storage/' . $imagePath);
            }
        }
        
        return asset('images/no-image.png');
    }

    /**
     * Method untuk mendapatkan URL chapter
     * TODO: Uncomment this when blade view is ready
     */
    // public function getUrlAttribute()
    // {
    //     return route('chapter.show', $this->slug);
    // }
}