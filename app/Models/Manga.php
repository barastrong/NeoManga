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
}