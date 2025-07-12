<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model
{
    use HasFactory;
    protected $table = 'bookmarks';
    protected $fillable = [
        'user_id',
        'manga_id',
        'bookmark',
    ];

    /**
     * Relasi: Bookmark milik satu User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi: Bookmark milik satu Manga
     */
    public function manga()
    {
        return $this->belongsTo(Manga::class);
    }
}
