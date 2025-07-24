<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manga extends Model
{
    use HasFactory;

    protected $table = 'mangas';

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'author',
        'artist',
        'status',
        'cover_image',
        'type',
    ];

    /**
     * Relasi: Manga memiliki banyak Genre (Many-to-Many)
     */
    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'manga_genres');
    }

    /**
     * Relasi: Manga dibuat oleh satu User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi: Manga memiliki banyak Rating
     */
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * Relasi: Manga memiliki banyak Bookmark
     */
    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }

    /**
     * Relasi: Manga memiliki banyak Chapter
     */
    public function chapters()
    {
        return $this->hasMany(Chapter::class);
    }

    /**
     * Accessor untuk mendapatkan jumlah followers (bookmarks)
     */
    public function getFollowersCountAttribute()
    {
        return $this->bookmarks()->count();
    }

    /**
     * Method untuk cek apakah manga sudah dibookmark oleh user tertentu
     */
    public function isBookmarkedBy($userId)
    {
        if (!$userId) return false;
        
        return $this->bookmarks()->where('user_id', $userId)->exists();
    }
    // Di dalam User.php (default Laravel model)

public function comments()
{
    return $this->hasMany(Comment::class);
}

    public function latestPublishedChapter()
    {
        // Di sini kita memanggil scopePublished()
        return $this->hasOne(Chapter::class)->published()->latest('id');
    }
}