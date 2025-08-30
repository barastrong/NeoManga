<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Models\User;

class ApiCommentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'manga_id' => 'required|exists:mangas,id',
            'chapter_id' => 'required|exists:chapters,id',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $user = $request->user();
        $rawContent = $validated['content'];

        $processedContent = preg_replace_callback(
            '/@([a-zA-Z0-9_]+)/',
            function ($matches) use ($user, &$rawContent) {
                $startWord = $matches[1];
                
                $mentionedUser = User::where('name', $startWord)->first();
                if ($user->isAdmin()) {
                    $fullTextAfterMention = substr($rawContent, strpos($rawContent, $matches[0]) + strlen($matches[0]));
                    $wordsAfter = explode(' ', trim($fullTextAfterMention));
                    $potentialName = $startWord;
                    $bestMatchUser = $mentionedUser;
                    $wordsConsumed = 0;
                    
                    for ($i = 0; $i < min(4, count($wordsAfter)); $i++) {
                        $potentialName .= ' ' . $wordsAfter[$i];
                        $tempUser = User::where('name', $potentialName)->first();
                        
                        if ($tempUser) {
                            $bestMatchUser = $tempUser;
                            $wordsConsumed = $i + 1;
                        }
                    }
                    
                    $mentionedUser = $bestMatchUser;

                    if ($mentionedUser && $wordsConsumed > 0) {
                        $originalText = $matches[0] . ' ' . implode(' ', array_slice($wordsAfter, 0, $wordsConsumed));
                        $colorClass = $mentionedUser->isAdmin() ? 'text-red-500 dark:text-red-400' : 'text-indigo-500 dark:text-indigo-400';
                        $replacement = '<a href="#" class="font-semibold ' . $colorClass . ' hover:underline">@' . e($mentionedUser->name) . '</a>';
                        $rawContent = str_replace($originalText, $replacement, $rawContent);
                        return '';
                    }
                }
                
                if ($mentionedUser) {
                    $colorClass = $mentionedUser->isAdmin() ? 'text-red-500 dark:text-red-400' : 'text-indigo-500 dark:text-indigo-400';
                    return '<a href="#" class="font-semibold ' . $colorClass . ' hover:underline">@' . e($mentionedUser->name) . '</a>';
                }

                return $matches[0];
            },
            $rawContent
        );
        
        $finalContent = ($user->isAdmin() && str_contains($rawContent, '<a href')) ? $rawContent : $processedContent;

        $validated['content'] = $finalContent;
        $comment = $user->comments()->create($validated);
        
        return response()->json($comment->load('user', 'replies.user'), 201);
    }

    public function toggleLike(Request $request, Comment $comment)
    {
        $user = $request->user();

        $result = $user->likedComments()->toggle($comment->id);

        $isLikedNow = count($result['attached']) > 0;

        $newLikesCount = $comment->likes()->count();

        $comment->likes_count = $newLikesCount;
        $comment->save();

        return response()->json([
            'liked' => $isLikedNow,
            'likes_count' => $newLikesCount,
        ]);
    }
    public function destroy(Request $request, Comment $comment)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Hanya admin yang dapat menghapus komentar.'], 403);
        }
        $comment->delete();
        return response()->json(null, 204);
    }
}