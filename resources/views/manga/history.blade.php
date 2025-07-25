@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 md:px-6 md:py-10">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-white">History</h1>
        @if($histories->isNotEmpty())
            <form action="{{ route('history.clear') }}" method="POST" onsubmit="return confirm('Anda yakin ingin membersihkan semua riwayat? Aksi ini tidak dapat dibatalkan.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-md text-sm transition-colors">
                    Clear All history
                </button>
            </form>
        @endif
    </div>
    
    @if (session('success'))
        <div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded relative mb-6" role="alert">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($histories->isNotEmpty())
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-x-5 gap-y-8">
            @foreach($histories as $history)
                @if($history->manga)
                    <div>
                        <div class="relative group">
                            <form action="{{ route('history.destroy', $history->id) }}" method="POST" class="absolute top-2 right-2 z-20">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Hapus dari riwayat" class="bg-red-600/80 hover:bg-red-600 text-white rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-all duration-300 transform group-hover:scale-100 scale-90 shadow-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm4 0a1 1 0 012 0v6a1 1 0 11-2 0V8z" clip-rule="evenodd"></path></svg>
                                </button>
                            </form>
                            
                            <a href="{{ route('manga.show', $history->manga->slug) }}">
                                <div class="relative aspect-[3/4] rounded-md overflow-hidden shadow-lg bg-gray-800">
                                    @if($history->manga->cover_image)
                                        <img src="{{ asset('storage/' . $history->manga->cover_image) }}" 
                                             alt="{{ $history->manga->title }}" 
                                             class="w-full h-full object-cover transition-transform duration-300 ease-in-out group-hover:scale-105">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                    @endif

                                    <div class="absolute top-2 right-2">
                                        @if($history->manga->type === 'manga')
                                            <img src="{{ asset('images/flags/jp.svg') }}" alt="Manga" class="w-6 h-6 rounded-full shadow-md" title="Manga (Japan)">
                                        @elseif($history->manga->type === 'manhwa')
                                            <img src="{{ asset('images/flags/kr.svg') }}" alt="Manhwa" class="w-6 h-6 rounded-full shadow-md" title="Manhwa (Korea)">
                                        @elseif($history->manga->type === 'manhua')
                                            <img src="{{ asset('images/flags/cn.svg') }}" alt="Manhua" class="w-6 h-6 rounded-full shadow-md" title="Manhua (China)">
                                        @endif
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="mt-3 text-white">
                            <a href="{{ route('manga.show', $history->manga->slug) }}">
                                <h3 class="font-bold text-base leading-tight truncate hover:text-blue-400 transition-colors" title="{{ $history->manga->title }}">
                                    {{ $history->manga->title }}
                                </h3>
                            </a>
                            
                            @if($history->chapter)
                                <a href="{{ route('chapter.show', $history->chapter->slug ) }}" class="hover:text-blue-400 transition-colors">
                                    <div class="flex justify-between items-center text-sm mt-2 border rounded-md px-2 py-1">
                                        Chapter {{ $history->chapter->number }}
                                        <span>
                                            {{ $history->created_at->diffForHumans(['short' => true, 'parts' => 1]) }}
                                        </span>
                                    </div>
                                </a>
                            @else
                                <p class="mt-2 text-sm italic">Info chapter tidak tersedia</p>
                            @endif

                            {{-- Rating Bintang diambil dari manga --}}
                            <div class="flex items-center mt-2">
                                @php $rounded_rating = round($history->manga->ratings_avg_rating * 2) / 2; @endphp
                                @for ($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= $rounded_rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                @endfor
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <div class="mt-10">
            {{ $histories->links() }}
        </div>
    @else
        <div class="text-center py-20 border-2 border-dashed border-gray-700 rounded-lg">
            <svg class="w-16 h-16 mx-auto mb-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <h3 class="text-xl font-medium mb-2">Riwayat Baca Anda Kosong</h3>
            <p class="text-sm">Manga yang Anda baca akan muncul di sini.</p>
        </div>
    @endif
</div>
@endsection