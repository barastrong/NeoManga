@extends('layouts.app')

@section('title', 'Bookmark -  NeoManga')

@section('content')

@auth
<div class="container mx-auto px-4 py-8 md:px-6 md:py-10">
    
    @if (session('success'))
        <div class="bg-green-100 dark:bg-green-500/20 border border-green-400 dark:border-green-500 text-green-700 dark:text-green-300 px-4 py-3 rounded relative mb-6" role="alert">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($bookmarks->isNotEmpty())
        {{-- Grid disesuaikan dengan jumlah paginate di controller --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-x-5 gap-y-8">
            @foreach($bookmarks as $bookmark)
                {{-- Pastikan relasi manga tidak null sebelum dirender --}}
                @if($bookmark->manga)
                    <div>
                        <div class="relative group"> {{-- Pindahkan 'group' ke parent agar hover bisa diakses tombol hapus --}}
                            <form action="{{ route('bookmark.destroy', $bookmark->id) }}" method="POST" class="absolute top-2 left-2 z-20">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Hapus dari riwayat" class="bg-red-600/80 hover:bg-red-600 text-white rounded-full p-1.5 shadow-lg transition-colors duration-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm4 0a1 1 0 012 0v6a1 1 0 11-2 0V8z" clip-rule="evenodd"></path></svg>
                                </button>
                            </form>
                            
                            <a href="{{ route('manga.show', $bookmark->manga->slug) }}">
                                <div class="relative aspect-[3/4] rounded-md overflow-hidden shadow-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                                    @if($bookmark->manga->cover_image)
                                        <img src="{{ asset('storage/' . $bookmark->manga->cover_image) }}" 
                                             alt="{{ $bookmark->manga->title }}" 
                                             class="w-full h-full object-cover transition-transform duration-300 ease-in-out group-hover:scale-105">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-gray-100 dark:bg-gray-800">
                                            <svg class="w-12 h-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                    @endif

                                    <div class="absolute top-2 right-2">
                                        @if($bookmark->manga->type === 'manga')
                                            <img src="https://flagcdn.com/w40/jp.png" alt="Manga" class="w-10 h-6 rounded-sm object-cover shadow-md" title="Manga (Japan)">
                                        @elseif($bookmark->manga->type === 'manhwa')
                                            <img src="https://flagcdn.com/w40/kr.png" alt="Manhwa" class="w-10 h-6 rounded-sm object-cover shadow-md" title="Manhwa (Korea)">
                                        @elseif($bookmark->manga->type === 'manhua')
                                            <img src="https://flagcdn.com/w40/cn.png" alt="Manhua" class="w-10 h-6 rounded-sm object-cover shadow-md" title="Manhua (China)">
                                        @endif
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="mt-3">
                            <a href="{{ route('manga.show', $bookmark->manga->slug) }}">
                                <h3 class="font-bold text-base leading-tight truncate text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors" title="{{ $bookmark->manga->title }}">
                                    {{ $bookmark->manga->title }}
                                </h3>
                            </a>
                            
                            @if($bookmark->manga->latestPublishedChapter)
                                {{-- Link menuju chapter yang terakhir dibaca --}}
                                <a href="{{ route('chapter.show', $bookmark->manga->latestPublishedChapter  ->slug ) }}" class="block text-sm mt-2 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                    <div class="flex justify-between items-center border border-gray-300 dark:border-gray-600 rounded-md px-2 py-1">
                                        <span>Chapter {{ $bookmark->manga->latestPublishedChapter->number }}</span>
                                        {{-- Waktu terakhir dibaca, menggunakan updated_at --}}
                                        <span class="text-xs text-gray-500">{{ $bookmark->updated_at->diffForHumans(['short' => true, 'parts' => 1]) }}</span>
                                    </div>
                                </a>
                            @else
                                <p class="mt-2 text-sm italic text-gray-500 dark:text-gray-400">Belum pernah dibaca</p>
                            @endif

                            {{-- Rating stars tidak perlu ada di halaman riwayat, tapi jika ingin dipertahankan tidak masalah --}}
                            <div class="flex items-center mt-2">
                                @php $rounded_rating = round($bookmark->manga->ratings_avg_rating * 2) / 2; @endphp
                                @for ($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= $rounded_rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                @endfor
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <div class="mt-10">
            {{-- PERBAIKAN 3: Gunakan variabel koleksi '$bookmarks' untuk pagination, bukan '$bookmark' --}}
            {{ $bookmarks->links() }}
        </div>
    @else
        <div class="text-center py-20 border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-lg">
            <svg class="w-16 h-16 mx-auto mb-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <h3 class="text-xl font-medium mb-2 text-gray-900 dark:text-gray-100">Riwayat Baca Anda Kosong</h3>
            <p class="text-gray-500 dark:text-gray-400 text-sm">Manga yang Anda bookmark atau baca akan muncul di sini.</p>
        </div>
    @endif
</div>
@endauth

@guest
<div class="container mx-auto px-4 py-8 md:px-6 md:py-10">
    <div class="text-center py-20 border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-lg">
        <svg class="w-16 h-16 mx-auto mb-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
        <h3 class="text-xl font-medium mb-2 text-gray-900 dark:text-gray-100">Akses Riwayat Baca Anda</h3>
        <p class="text-gray-500 dark:text-gray-400 mb-6">Silakan login untuk melihat dan mengelola riwayat manga yang telah Anda baca.</p>
        <a href="{{ route('login') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-md text-sm transition-colors">
            Login
        </a>
    </div>
</div>
@endguest

@endsection