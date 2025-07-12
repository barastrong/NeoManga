<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Manga;

class DashboardController extends Controller
{
    public function index() {
        $mangas = Manga::all();
        return view('dashboard', compact('mangas'));
    }
}
