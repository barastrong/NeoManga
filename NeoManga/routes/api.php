<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiProductController;
use App\Http\Controllers\Api\ChapterImportController;
use App\Http\Controllers\Api\ApiHistoryController;
use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiBookmarkController;
use App\Http\Controllers\Api\ApiCommentController;
use App\Http\Controllers\Api\ApiChapterController;

Route::post('/import-chapter', [ChapterImportController::class, 'store'])
     ->middleware('auth.apikey');
Route::get('/manga/check/{slug}', [ChapterImportController::class, 'checkMangaExists'])->middleware('auth.apikey');

Route::post('/register', [ApiAuthController::class, 'register']);
Route::post('/login', [ApiAuthController::class, 'login']);

Route::get('/mangas', [ApiProductController::class, 'index']) ;

Route::get('/manga/{manga:slug}', [ApiProductController::class, 'show']);

Route::get('/search', [ApiProductController::class, 'search']);
Route::get('/manga-list', [ApiProductController::class, 'mangaList']);
Route::get('/chapter/{chapter:slug}', [ApiChapterController::class, 'showApi']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [ApiAuthController::class, 'logout']);
    
    Route::get('/history', [ApiHistoryController::class, 'index']);
    Route::delete('/history/{history}', [ApiHistoryController::class, 'destroy'])->name('api.history.destroy');
    Route::delete('/history/clear', [ApiHistoryController::class, 'clearAll'])->name('api.history.clear');
    Route::delete('/history/manga/{mangaId}', [ApiHistoryController::class, 'clearForManga'])->name('api.history.resetForManga');
    
    Route::get('/bookmarks', [ApiBookmarkController::class, 'index']);
    Route::post('/manga/{manga:slug}/toggle-bookmark', [ApiBookmarkController::class, 'toggle']);
    Route::delete('/bookmarks/{bookmark}', [ApiBookmarkController::class, 'destroy']);

    Route::post('/comments', [ApiCommentController::class, 'store']);
    Route::post('/comments/{comment}/like', [ApiCommentController::class, 'toggleLike']);
    Route::delete('/comments/{comment}', [ApiCommentController::class, 'destroy']);
});