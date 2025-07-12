@extends('layouts.app')

@section('title', $chapter->manga->title . ' - Chapter ' . $chapter->number)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <nav class="flex items-center space-x-2 text-sm mb-4">
                <a href="{{ route('dashboard') }}" class="hover:underline">Home</a>
                <span>/</span>
                <a href="{{ route('manga.show', $chapter->manga->slug) }}" class="hover:underline">{{ $chapter->manga->title }}</a>
                <span>/</span>
                <span>Chapter {{ $chapter->number }}</span>
            </nav>
            
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-3xl font-bold">{{ $chapter->manga->title }}</h1>
            </div>
            
            <h2 class="text-xl font-semibold mb-4">Chapter {{ $chapter->number }}</h2>
            
            <div class="text-center mb-6 p-4 border rounded">
                <p class="text-sm leading-relaxed">
                    Read the latest manga <strong>{{ $chapter->manga->title }} Chapter {{ $chapter->number }} Bahasa Indonesia</strong> at <strong>NeoManga</strong>. 
                    Manga {{ $chapter->manga->title }} is always updated at NeoManga. 
                    Don't forget to read the other manga updates. A list of manga collections NeoManga is in the Content List menu.
                </p>
            </div>
        </div>

        <div class="flex items-center justify-between mb-6">
            <div class="flex space-x-2">
                @if($prevChapter)
                    <a href="{{ route('chapter.show', $prevChapter->slug) }}" 
                       class="px-4 py-2 border rounded hover:shadow-md transition-shadow">
                        ← Previous
                    </a>
                @endif
                
                @if($nextChapter)
                    <a href="{{ route('chapter.show', $nextChapter->slug) }}" 
                       class="px-4 py-2 border rounded hover:shadow-md transition-shadow">
                        Next →
                    </a>
                @endif
            </div>
            
            <div class="flex items-center space-x-4 text-sm">
                <span>{{ $chapter->image_count }} Images</span>
                <span>{{ $chapter->created_at->format('M d, Y') }}</span>
            </div>
        </div>

        <div class="space-y-4 mb-8">
            @foreach($chapter->image_urls as $index => $imageUrl)
                <div class="flex justify-center">
                    <img src="{{ $imageUrl }}" 
                         alt="Chapter {{ $chapter->number }} - Page {{ $index + 1 }}"
                         class="max-w-full h-auto rounded shadow-lg"
                         loading="lazy">
                </div>
            @endforeach
        </div>

        <div class="border-t pt-6">
            <div class="flex items-center justify-between">
                <div class="flex space-x-2">
                    @if($prevChapter)
                        <a href="{{ route('chapter.show', $prevChapter->slug) }}" 
                           class="px-4 py-2 border rounded hover:shadow-md transition-shadow">
                            ← Chapter {{ $prevChapter->number }}
                        </a>
                    @endif
                    
                    @if($nextChapter)
                        <a href="{{ route('chapter.show', $nextChapter->slug) }}" 
                           class="px-4 py-2 border rounded hover:shadow-md transition-shadow">
                            Chapter {{ $nextChapter->number }} →
                        </a>
                    @endif
                </div>
                
                <a href="{{ route('manga.show', $chapter->manga->slug) }}" 
                   class="px-4 py-2 border rounded hover:shadow-md transition-shadow">
                    Back to Manga
                </a>
            </div>
        </div>

        <div class="mt-8 p-4 border rounded">
            <h3 class="font-semibold mb-2">About this Chapter</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <strong>Manga:</strong> {{ $chapter->manga->title }}
                </div>
                <div>
                    <strong>Chapter:</strong> {{ $chapter->number }}
                </div>
                <div>
                    <strong>Status:</strong> {{ $chapter->status_label }}
                </div>
                <div>
                    <strong>Images:</strong> {{ $chapter->image_count }}
                </div>
                <div>
                    <strong>Published:</strong> {{ $chapter->created_at->format('M d, Y') }}
                </div>
                <div>
                    <strong>Updated:</strong> {{ $chapter->updated_at->format('M d, Y') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection