@extends('layouts.app')

@section('content')
<div class="min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 mb-8 shadow-lg border border-gray-200 dark:border-gray-700">
            <div class="flex flex-col md:flex-row gap-6">
                <div class="flex-shrink-0">
                    <img src="{{ $manga->cover_image ? asset('storage/' . $manga->cover_image) : asset('images/no-image.png') }}" 
                         alt="{{ $manga->title }}" 
                         class="w-48 h-64 object-cover rounded-lg shadow-lg">
                    
                    @auth
                        <button id="bookmarkBtn" 
                                data-manga-id="{{ $manga->id }}"
                                class="w-full mt-4 font-bold py-2 px-4 rounded-lg transition duration-200 text-white {{ $isBookmarked ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700' }}">
                            <i class="fas fa-bookmark mr-2"></i>
                            <span id="bookmarkText">{{ $isBookmarked ? 'Remove Bookmark' : 'Add Bookmark' }}</span>
                        </button>
                    @else
                        <a href="{{ route('login') }}" 
                           class="w-full mt-4 bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200 block text-center">
                            <i class="fas fa-bookmark mr-2"></i>
                            Login to Bookmark
                        </a>
                    @endauth
                    
                    <div class="mt-4 text-center">
                        <div class="text-sm mb-2 text-gray-700 dark:text-gray-300">
                            Followed by <span id="followersCount" class="font-semibold text-red-600">{{ $manga->followers_count }}</span> people
                        </div>
                        <div class="flex justify-center space-x-1">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star text-yellow-400"></i>
                            @endfor
                        </div>
                    </div>
                </div>

                <div class="flex-1">
                    <h1 class="text-3xl font-bold mb-4 text-gray-800 dark:text-white">{{ $manga->title }}</h1>
                    
                    <p class="mb-6 leading-relaxed text-gray-700 dark:text-gray-300">
                        {{ $manga->description }}
                    </p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <div class="flex">
                                <span class="w-20 text-gray-600 dark:text-gray-400 font-medium">Status:</span>
                                <span class="capitalize text-gray-800 dark:text-gray-200">{{ $manga->status }}</span>
                            </div>
                            <div class="flex">
                                <span class="w-20 text-gray-600 dark:text-gray-400 font-medium">Type:</span>
                                <span class="capitalize text-gray-800 dark:text-gray-200">{{ $manga->type }}</span>
                            </div>
                            <div class="flex">
                                <span class="w-20 text-gray-600 dark:text-gray-400 font-medium">Released:</span>
                                <span class="text-gray-800 dark:text-gray-200">{{ $manga->created_at->format('Y') }}</span>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="flex">
                                <span class="w-20 text-gray-600 dark:text-gray-400 font-medium">Author:</span>
                                <span class="text-gray-800 dark:text-gray-200">{{ $manga->author }}</span>
                            </div>
                            <div class="flex">
                                <span class="w-20 text-gray-600 dark:text-gray-400 font-medium">Artist:</span>
                                <span class="text-gray-800 dark:text-gray-200">{{ $manga->artist ?? 'N/A' }}</span>
                            </div>
                            <div class="flex">
                                <span class="w-20 text-gray-600 dark:text-gray-400 font-medium">Posted By:</span>
                                <span class="text-gray-800 dark:text-gray-200">{{ $manga->user->name }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 space-y-2">
                        <div class="flex">
                            <span class="w-24 text-gray-600 dark:text-gray-400 font-medium">Posted On:</span>
                            <span class="text-gray-800 dark:text-gray-200">{{ $manga->created_at->format('d M Y') }}</span>
                        </div>
                        <div class="flex">
                            <span class="w-24 text-gray-600 dark:text-gray-400 font-medium">Updated On:</span>
                            <span class="text-gray-800 dark:text-gray-200">{{ $manga->updated_at->format('d M Y') }}</span>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <div class="flex flex-wrap gap-2">
                            @foreach($manga->genres as $genre)
                                <span class="bg-red-600 hover:bg-red-700 px-3 py-1 rounded-full text-sm text-white transition-colors duration-200">
                                    {{ $genre->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-lg border border-gray-200 dark:border-gray-700">
            <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white">Chapter {{ $manga->title }}</h2>
            
            @if($chapters->count() > 0)
                <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3">
                    @foreach($chapters as $chapter)
                        @if($chapter->status == 'published' || $chapter->status == 'fixed')
                            <div class="bg-gray-50 hover:bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 rounded p-3 transition duration-200 border border-gray-200 dark:border-gray-600">
                                <a href="{{ route('chapter.show', $chapter->slug) }}" 
                                   class="block hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                    <div class="flex items-center justify-between">
                                        <div class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                            Chapter {{ $chapter->number }}
                                        </div>
                                        @if($chapter->status == 'fixed')
                                            <span class="text-xs bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 px-2 py-1 rounded">
                                                Fixed
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ $chapter->created_at->format('d M Y') }}
                                    </div>
                                </a>
                            </div>
                        @endif
                    @endforeach
                </div>
                
                @if($chapters->count() > 20)
                    <div class="mt-6 text-center">
                        <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition duration-200">
                            Load More Chapters
                        </button>
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <div class="text-lg text-gray-600 dark:text-gray-400">No chapters available yet</div>
                    <div class="text-sm mt-2 text-gray-500 dark:text-gray-500">Check back later for updates</div>
                </div>
            @endif
        </div>
    </div>
</div>

@auth
<script>
document.addEventListener('DOMContentLoaded', function() {
    const bookmarkBtn = document.getElementById('bookmarkBtn');
    const bookmarkText = document.getElementById('bookmarkText');
    const followersCount = document.getElementById('followersCount');
    
    if (bookmarkBtn) {
        bookmarkBtn.addEventListener('click', function() {
            const mangaId = this.dataset.mangaId;
            
            this.disabled = true;
            
            fetch(`/bookmark/toggle/${mangaId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.is_bookmarked) {
                        this.className = 'w-full mt-4 font-bold py-2 px-4 rounded-lg transition duration-200 bg-green-600 hover:bg-green-700';
                        bookmarkText.textContent = 'Remove Bookmark';
                    } else {
                        this.className = 'w-full mt-4 font-bold py-2 px-4 rounded-lg transition duration-200 bg-red-600 hover:bg-red-700';
                        bookmarkText.textContent = 'Add Bookmark';
                    }
                    
                    followersCount.textContent = data.followers_count;
                } else {
                    alert(data.message || 'An error occurred');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while processing your request');
            })
            .finally(() => {
                this.disabled = false;
            });
        });
    }
});
</script>
@endauth
@endsection