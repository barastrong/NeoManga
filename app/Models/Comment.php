<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'manga_id',
        'chapter_id',
        'content',
        'parent_id',
        'likes_count'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function manga()
    {
        return $this->belongsTo(Manga::class);
    }

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function isLikedBy(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $likedComments = session()->get('liked_comments', []);

        return in_array($this->id, $likedComments);
    }

}