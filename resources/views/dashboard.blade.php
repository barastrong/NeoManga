@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 md:px-6 md:py-10">
    @if($mangas->count() > 0)
        <div>
            <h2 class="text-2xl font-bold mb-6">
                Update Terbaru
            </h2>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-x-4 gap-y-6 md:gap-x-5 md:gap-y-8">
                @foreach($mangas as $manga)
                    <div>
                        <a href="{{ route('manga.show', $manga->slug) }}" class="group block">
                            <div class="aspect-[3/4] rounded-md overflow-hidden shadow-lg hover:shadow-xl transition-shadow duration-300">
                                @if($manga->cover_image)
                                    <img src="{{ asset('storage/' . $manga->cover_image) }}" 
                                         alt="{{ $manga->title }}" 
                                         class="w-full h-full object-cover transition-transform duration-300 ease-in-out group-hover:scale-105">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gray-200">
                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        </a>
                        
                        <div class="mt-2 p-2 border border-gray-400 rounded-md">
                            <a href="{{ route('manga.show', $manga->slug) }}" class="block">
                                <h3 class="font-bold  text-base leading-tight truncate hover:text-blue-600 transition-colors" title="{{ $manga->title }}">
                                    {{ $manga->title }}
                                </h3>
                            </a>
                            
                            @if($manga->latestPublishedChapter)
                                <div class="flex justify-between items-center text-sm">
                                    <a href="{{ route('chapter.show', $manga->latestPublishedChapter->slug ) }}">
                                    <span class="font-medium hover:text-blue-600 transition-colors ">
                                        Chapter {{ $manga->latestPublishedChapter->number }}
                                    </span>
                                    </a>
                                    <span class="text-xs">
                                        {{ $manga->latestPublishedChapter->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            @else
                                <p class="mt-1 text-sm text-gray-400 italic">Belum ada chapter</p>
                            @endif

                            <div class="flex items-center mt-1.5  text-xs">
                                <svg class="w-4 h-4 mr-1 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                @if($manga->ratings_avg_rating !== null)
                                    <span class="font-medium">{{ number_format($manga->ratings_avg_rating, 1) }}</span>
                                @else
                                    <span class="opacity-80">N/A</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>


            <div class="mt-10">
                {{-- $mangas->links() --}}
            </div>
        </div>
    @else
        <div class="text-center py-20">
            <svg class="w-20 h-20 mx-auto mb-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            <h3 class="text-xl font-medium mb-2">Belum ada manga</h3>
            <p class="text-sm text-gray-500">Manga akan muncul di sini setelah ditambahkan.</p>
        </div>
    @endif
</div>
@endsection
