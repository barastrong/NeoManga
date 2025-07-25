@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 md:px-6 md:py-10">
    <form method="GET" action="{{ route('manga.list') }}" id="filterForm">
        <div class="p-4 mb-8 rounded-lg shadow-sm bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                {{-- Tombol Genre --}}
                <div>
                    <label for="genreBtn" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Genre</label>
                    <button 
                        type="button" 
                        id="genreBtn"
                        class="w-full h-10 flex items-center justify-between text-left px-3 py-2 text-base border rounded-md shadow-sm bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                    >
                        <span class="truncate text-gray-900 dark:text-gray-100" id="genreButtonText">
                            @php
                                $selectedGenres = request('genre', []);
                                $selectedGenreCount = is_array($selectedGenres) ? count($selectedGenres) : 0;
                            @endphp
                            @if($selectedGenreCount > 0) 
                                {{ $selectedGenreCount }} genre selected
                            @else 
                                Pilih Genre 
                            @endif
                        </span>
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                    </button>
                </div>

                {{-- Filter Lainnya (Status, Type, Order) --}}
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select onchange="this.form.submit()" id="status" name="status" class="w-full h-10 px-3 py-2 text-base border rounded-md shadow-sm bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-gray-100">
                        <option value="">All</option>
                        <option value="ongoing" @if(request('status') == 'ongoing') selected @endif>Ongoing</option>
                        <option value="completed" @if(request('status') == 'completed') selected @endif>Completed</option>
                    </select>
                </div>
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
                    <select onchange="this.form.submit()" id="type" name="type" class="w-full h-10 px-3 py-2 text-base border rounded-md shadow-sm bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-gray-100">
                        <option value="">All</option>
                        <option value="manga" @if(request('type') == 'manga') selected @endif>Manga</option>
                        <option value="manhwa" @if(request('type') == 'manhwa') selected @endif>Manhwa</option>
                        <option value="manhua" @if(request('type') == 'manhua') selected @endif>Manhua</option>
                    </select>
                </div>
                <div>
                    <label for="order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Order</label>
                    <select onchange="this.form.submit()" id="order" name="order" class="w-full h-10 px-3 py-2 text-base border rounded-md shadow-sm bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-gray-100">
                        <option value="latest" @if(request('order', 'latest') == 'latest') selected @endif>Latest</option>
                        <option value="popularity" @if(request('order') == 'popularity') selected @endif>Popularity</option>
                        <option value="rating" @if(request('order') == 'rating') selected @endif>Rating</option>
                    </select>
                </div>
            </div>
        </div>
        
        {{-- Modal Genre --}}
        <div id="genreModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 hidden">
            <div id="genreModalContent" class="w-full max-w-2xl mx-4 bg-white dark:bg-gray-800 rounded-lg shadow-xl flex flex-col">
                <div class="p-5 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Pilih Genre</h3>
                </div>
                <div class="p-6 max-h-[60vh] overflow-y-auto">
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                        @foreach($genres as $genre)
                            @php
                                $selectedGenres = request('genre', []);
                                $isChecked = is_array($selectedGenres) && in_array($genre->id, array_map('intval', $selectedGenres));
                            @endphp
                            <label class="flex items-center space-x-3 p-3 rounded-lg border cursor-pointer transition-colors genre-checkbox-label @if($isChecked) bg-blue-50 dark:bg-blue-900/30 border-blue-500 @else border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 @endif">
                                <input 
                                    type="checkbox" 
                                    name="genre[]" 
                                    value="{{ $genre->id }}" 
                                    @if($isChecked) checked @endif 
                                    class="genre-checkbox h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                >
                                <span class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $genre->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 flex justify-end items-center space-x-3">
                    <button type="button" id="cancelGenreBtn" class="px-4 py-2 border border-gray-300 text-gray-700 dark:text-gray-300 dark:border-gray-600 text-sm font-medium rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Batal
                    </button>
                    <button type="button" id="applyGenreBtn" class="px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-50 dark:focus:ring-offset-gray-900 focus:ring-blue-500">
                        Terapkan
                    </button>
                </div>
            </div>
        </div>
    </form>
    
    {{-- Tampilan Manga --}}
    @if($mangas->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-x-5 gap-y-8">
            @foreach($mangas as $manga)
                <div>
                    <a href="{{ route('manga.show', $manga->slug) }}" class="block group">
                        <div class="relative aspect-[3/4] rounded-md overflow-hidden shadow-lg bg-gray-200 dark:bg-gray-800">
                            @if($manga->cover_image)
                                <img src="{{ asset('storage/' . $manga->cover_image) }}" alt="{{ $manga->title }}" class="w-full h-full object-cover transition-transform duration-300 ease-in-out group-hover:scale-105">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            @endif
                            <div class="absolute top-2 right-2">
                                @if($manga->type === 'manga')
                                    <img src="https://flagcdn.com/w40/jp.png" alt="Manga" class="w-10 h-6 rounded-sm object-cover shadow-md" title="Manga (Japan)">
                                @elseif($manga->type === 'manhwa')
                                    <img src="https://flagcdn.com/w40/kr.png" alt="Manhwa" class="w-10 h-6 rounded-sm object-cover shadow-md" title="Manhwa (Korea)">
                                @elseif($manga->type === 'manhua')
                                    <img src="https://flagcdn.com/w40/cn.png" alt="Manhua" class="w-10 h-6 rounded-sm object-cover shadow-md" title="Manhua (China)">
                                @endif
                            </div>
                        </div>
                    </a>
                    <div class="mt-3">
                        <a href="{{ route('manga.show', $manga->slug) }}">
                            <h3 class="font-bold text-base leading-tight truncate text-gray-900 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400 transition-colors" title="{{ $manga->title }}">
                                {{ $manga->title }}
                            </h3>
                        </a>
                        @if($manga->latestPublishedChapter)
                        <a href="{{ route('chapter.show', $manga->latestPublishedChapter->slug ) }}" class="group/chapter">
                            <div class="flex justify-between items-center text-sm mt-2 border rounded-md px-2 py-1 text-gray-600 dark:text-gray-400 border-gray-300 dark:border-gray-600 group-hover/chapter:border-blue-500 group-hover/chapter:text-blue-600 dark:group-hover/chapter:text-blue-400 transition-colors">
                                <span>Chapter {{ $manga->latestPublishedChapter->number }}</span>
                                <span>{{ $manga->latestPublishedChapter->created_at->diffForHumans(['short' => true, 'parts' => 1]) }}</span>
                            </div>
                        </a>
                        @else
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-500 italic">Belum ada chapter</p>
                        @endif
                        <div class="flex items-center mt-2">
                            @for ($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 @if($i <= round($manga->ratings_avg_rating * 2) / 2) text-yellow-400 @else text-gray-300 dark:text-gray-600 @endif" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            @endfor
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-10">
            {{ $mangas->links() }}
        </div>
    @else
        <div class="text-center py-20 rounded-lg bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            <h3 class="text-xl font-medium mb-2 text-gray-900 dark:text-gray-100">Tidak Ada Hasil Ditemukan</h3>
            <p class="text-gray-600 dark:text-gray-400">Coba ubah filter pencarian Anda.</p>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const genreBtn = document.getElementById('genreBtn');
        const genreModal = document.getElementById('genreModal');
        const applyGenreBtn = document.getElementById('applyGenreBtn');
        const cancelGenreBtn = document.getElementById('cancelGenreBtn');
        const mainForm = document.getElementById('filterForm');
        const genreCheckboxes = document.querySelectorAll('.genre-checkbox');
        const genreButtonText = document.getElementById('genreButtonText');

        // Get initial selected genres from URL
        const urlParams = new URLSearchParams(window.location.search);
        const initialSelectedGenres = urlParams.getAll('genre[]').concat(urlParams.getAll('genre')).map(id => parseInt(id));

        function openModal() {
            genreModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            genreModal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
        
        function resetGenresToInitialState() {
            genreCheckboxes.forEach(checkbox => {
                const genreId = parseInt(checkbox.value);
                checkbox.checked = initialSelectedGenres.includes(genreId);
            });
            updateCheckboxStyles();
        }

        function updateCheckboxStyles() {
            document.querySelectorAll('.genre-checkbox-label').forEach(label => {
                const checkbox = label.querySelector('.genre-checkbox');
                if (checkbox.checked) {
                    label.classList.add('bg-blue-50', 'dark:bg-blue-900/30', 'border-blue-500');
                    label.classList.remove('border-gray-300', 'dark:border-gray-600');
                } else {
                    label.classList.remove('bg-blue-50', 'dark:bg-blue-900/30', 'border-blue-500');
                    label.classList.add('border-gray-300', 'dark:border-gray-600');
                }
            });
        }

        function updateButtonText() {
            const checkedCount = document.querySelectorAll('.genre-checkbox:checked').length;
            if (checkedCount > 0) {
                genreButtonText.textContent = `${checkedCount} genre selected`;
            } else {
                genreButtonText.textContent = 'Pilih Genre';
            }
        }

        if (genreBtn && genreModal) {
            genreBtn.addEventListener('click', openModal);

            genreCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    updateCheckboxStyles();
                    updateButtonText();
                });
            });

            applyGenreBtn.addEventListener('click', function() {
                mainForm.submit();
            });

            cancelGenreBtn.addEventListener('click', function() {
                resetGenresToInitialState();
                updateButtonText();
                closeModal();
            });

            genreModal.addEventListener('click', (event) => {
                if (event.target === genreModal) {
                    resetGenresToInitialState();
                    updateButtonText();
                    closeModal();
                }
            });
            
            // Initialize styles and button text
            updateCheckboxStyles();
            updateButtonText();
        }

        // Handle escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !genreModal.classList.contains('hidden')) {
                resetGenresToInitialState();
                updateButtonText();
                closeModal();
            }
        });
    });
</script>
@endpush