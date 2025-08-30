@extends('layouts.adminSidebar')

@section('content')
    <h1 class="text-3xl font-bold mb-6">Dashboard</h1>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold text-gray-700">Total Manga</h2>
            <p class="text-3xl font-bold mt-2">{{ $mangaCount }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold text-gray-700">Total Chapters</h2>
            <p class="text-3xl font-bold mt-2">{{ $chapterCount }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold text-gray-700">Total Users</h2>
            <p class="text-3xl font-bold mt-2">{{ $userCount }}</p>
        </div>
    </div>

    <!-- Latest Manga -->
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Manga Terbaru</h2>
        <table class="w-full text-left">
            <thead>
                <tr>
                    <th class="py-2">Title</th>
                    <th class="py-2">Status</th>
                    <th class="py-2">Ditambahkan pada</th>
                </tr>
            </thead>
            <tbody>
                @forelse($latestMangas as $manga)
                    <tr class="border-t">
                        <td class="py-2">{{ $manga->title }}</td>
                        <td class="py-2"><span class="px-2 py-1 text-xs font-semibold rounded-full {{ $manga->status === 'ongoing' ? 'bg-green-200 text-green-800' : 'bg-blue-200 text-blue-800' }}">{{ ucfirst($manga->status) }}</span></td>
                        <td class="py-2">{{ $manga->created_at->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="py-4 text-center text-gray-500">Belum ada manga.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection