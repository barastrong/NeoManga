<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookmarkController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MangaController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\DashboardController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/content/{slug}', [MangaController::class, 'show'])->name('manga.show');
Route::get('/chapter/{slug}', [ChapterController::class, 'show'])->name('chapter.show');
Route::get('/manga/{manga}/comments', [CommentController::class, 'getMangaComments'])->name('comments.manga');
Route::get('/chapter/{chapter}/comments', [CommentController::class, 'getChapterComments'])->name('comments.chapter');
Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
Route::post('/comments/{comment}/reply', [CommentController::class, 'reply'])->name('comments.reply');
Route::post('/comments/{comment}/like', [CommentController::class, 'toggleLike'])->name('comments.like');
Route::get('/manga', [MangaController::class, 'mangaList'])->name('manga.list');
Route::get('/bookmarks', [BookmarkController::class, 'index'])->name('bookmark.index');
Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
Route::get('/search', [MangaController::class, 'search'])->name('manga.search');

Route::middleware('auth', 'verified')->group(function () {
    Route::post('/bookmark/toggle/{manga}', [BookmarkController::class, 'toggle'])->name('bookmark.toggle');
    Route::delete('/bookmark/{bookmark}', [BookmarkController::class, 'destroy'])->name('bookmark.destroy');
    Route::get('profile/show', [ProfileController::class, 'show'])->name('user.profile');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::delete('/history/{history}', [HistoryController::class, 'destroy'])->name('history.destroy');
    Route::delete('/history', [HistoryController::class, 'clear'])->name('history.clear');
    Route::post('/manga/{mangaId}/history/reset', [HistoryController::class, 'resetForManga'])->name('history.resetForManga');

});

require __DIR__.'/auth.php';