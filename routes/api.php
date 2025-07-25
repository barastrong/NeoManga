<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ChapterImportController;

Route::post('/import-chapter', [ChapterImportController::class, 'store'])
     ->middleware('auth.apikey');

     
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
