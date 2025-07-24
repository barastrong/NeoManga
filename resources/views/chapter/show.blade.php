@extends('layouts.app')

@section('title', $chapter->manga->title . ' - Chapter ' . $chapter->number)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        {{-- Bagian Atas: Navigasi dan Info Chapter --}}
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

        {{-- Navigasi Chapter --}}
        <div class="flex items-center justify-between mb-6">
            <a href="{{ route('manga.show', $chapter->manga->slug) }}" class="px-4 py-2 border rounded hover:shadow-md transition-shadow">
                Back to Manga
            </a>
            <div class="flex space-x-2">
                @if($prevChapter)
                    <a href="{{ route('chapter.show', $prevChapter->slug) }}" class="px-4 py-2 border rounded hover:shadow-md transition-shadow">
                        ← Previous
                    </a>
                @endif
                @if($nextChapter)
                    <a href="{{ route('chapter.show', $nextChapter->slug) }}" class="px-4 py-2 border rounded hover:shadow-md transition-shadow">
                        Next →
                    </a>
                @endif
            </div>
            <div class="flex items-center space-x-4 text-sm">
                <span>{{ $chapter->image_count }} Images</span>
                <span>{{ $chapter->created_at->format('M d, Y') }}</span>
            </div>
        </div>

        {{-- Tampilan Gambar Chapter --}}
        <div class="space-y-4 mb-8">
            @foreach($chapter->image_urls as $index => $imageUrl)
                <div class="flex justify-center">
                    <img src="{{ $imageUrl }}" alt="Chapter {{ $chapter->number }} - Page {{ $index + 1 }}" class="max-w-full h-auto rounded shadow-lg" loading="lazy">
                </div>
            @endforeach
        </div>

        {{-- Navigasi Bawah --}}
        <div class="border-t pt-6 mb-12">
            <div class="flex items-center justify-between">
                <div class="flex space-x-2">
                    @if($prevChapter)
                        <a href="{{ route('chapter.show', $prevChapter->slug) }}" class="px-4 py-2 border rounded hover:shadow-md transition-shadow">
                            ← Chapter {{ $prevChapter->number }}
                        </a>
                    @endif
                    @if($nextChapter)
                        <a href="{{ route('chapter.show', $nextChapter->slug) }}" class="px-4 py-2 border rounded hover:shadow-md transition-shadow">
                            Chapter {{ $nextChapter->number }} →
                        </a>
                    @endif
                </div>
                <a href="{{ route('manga.show', $chapter->manga->slug) }}" class="px-4 py-2 border rounded hover:shadow-md transition-shadow">
                    Back to Manga
                </a>
            </div>
        </div>

        <!-- ==================================================== -->
        <!--     BAGIAN KOMENTAR YANG SUDAH DIPERBAIKI TOTAL      -->
        <!-- ==================================================== -->
        <div  id="comments-section" class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-lg border border-gray-200 dark:border-gray-700">
            <!-- [FIX 1] Menggunakan variabel $totalCommentsCount dari ChapterController -->
            <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white">Comments ({{ $totalCommentsCount }})</h2>

            @auth
                <form action="{{ route('comments.store') }}" method="POST" class="mb-8">
                    @csrf
                    <!-- [FIX 2] Mengambil ID dari variabel $chapter dan relasinya -->
                    <input type="hidden" name="manga_id" value="{{ $chapter->manga->id }}">
                    <input type="hidden" name="chapter_id" value="{{ $chapter->id }}">
                    <div class="flex items-start space-x-4">
                        <img class="w-10 h-10 rounded-full object-cover" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=random&color=fff" alt="Avatar">
                        <div class="flex-1">
                            <textarea name="content" rows="3" class="w-full p-3 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-red-500 focus:border-red-500 transition" placeholder="Add a public comment for this chapter..." required></textarea>
                            <div class="text-right mt-2">
                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                                    Post Comment
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            @else
                <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-8 text-center mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Join the Discussion!</h3>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">You must be logged in to post a comment.</p>
                    <a href="{{ route('login') }}">
                        <button class="mt-4 bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                            Login to Comment
                        </button>
                    </a>
                </div>
            @endauth

            <div class="space-y-6">
                <!-- [FIX 3] Menggunakan variabel $comments yang sudah siap dari ChapterController -->
                @forelse ($comments as $comment)
                    <div id="comment-{{ $comment->id }}" class="flex items-start space-x-4">
                        <img class="w-10 h-10 rounded-full object-cover" src="https://ui-avatars.com/api/?name={{ urlencode($comment->user->name) }}&background=random&color=fff" alt="Avatar">
                        <div class="flex-1">
                            <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        @if ($comment->user->isAdmin())
                                            <span class="font-semibold text-red-500 dark:text-red-400">{{ $comment->user->name }}</span>
                                            <span class="bg-red-500 text-white text-xs font-semibold px-2 py-0.5 rounded-full">
                                                <i class="fas fa-shield-alt fa-fw mr-1"></i>NeoAdmin
                                            </span>
                                        @else
                                            <span class="font-semibold text-gray-800 dark:text-white">{{ $comment->user->name }}</span>
                                        @endif
                                    </div>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 flex-shrink-0 ml-4">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="mt-2 flex justify-between items-end">
                                    <div class="flex-grow">
                                        {{-- Ini adalah pengganti dari partials/comment-content --}}
                                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $comment->content }}</p>
                                    </div>
                                    @if (auth()->check() && auth()->user()->isAdmin())
                                        <form id="delete-form-{{ $comment->id }}" action="{{ route('comments.destroy', $comment->id) }}" method="POST" class="ml-4 flex-shrink-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" data-form-id="delete-form-{{ $comment->id }}" class="delete-comment-btn text-gray-400 dark:text-gray-500 text-sm transition-all duration-200 hover:text-red-500 dark:hover:text-red-400 hover:scale-125" title="Delete Comment">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                            @auth
                            <div class="flex items-center space-x-4 mt-2 pl-2 text-sm">
                                <button data-comment-id="{{ $comment->id }}" class="like-btn font-medium text-gray-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-500 transition">
                                    <i class="fa-heart {{ $comment->isLikedBy() ? 'fas text-red-500' : 'far' }}"></i>
                                    <span id="like-count-{{ $comment->id }}">{{ $comment->likes_count }}</span>
                                </button>
                                <button data-comment-id="{{ $comment->id }}" data-username="{{ $comment->user->name }}" class="reply-btn font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
                                    Reply
                                </button>
                            </div>
                            @endauth
                            <div id="reply-form-{{ $comment->id }}" class="mt-4 ml-4" style="display: none;">
                                <form action="{{ route('comments.reply', $comment->id) }}" method="POST">
                                    @csrf
                                    <textarea name="content" rows="2" class="w-full p-2 bg-gray-100 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-red-500 focus:border-red-500 transition" placeholder="Write a reply..." required></textarea>
                                    <div class="text-right mt-2"><button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-1 px-3 rounded-lg">Post Reply</button></div>
                                </form>
                            </div>
                            <div class="mt-4 ml-8 space-y-4">
                                @foreach ($comment->replies as $reply)
                                <div id="comment-{{ $reply->id }}" class="flex items-start space-x-3">
                                    <img class="w-8 h-8 rounded-full object-cover" src="https://ui-avatars.com/api/?name={{ urlencode($reply->user->name) }}&background=random&color=fff" alt="Avatar">
                                    <div class="flex-1">
                                        <div class="bg-gray-100 dark:bg-gray-700/50 rounded-lg p-3">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-2">
                                                    @if ($reply->user->isAdmin())
                                                        <span class="font-semibold text-red-500 dark:text-red-400 text-sm">{{ $reply->user->name }}</span>
                                                        <span class="bg-red-500 text-white text-xs font-semibold px-2 py-0.5 rounded-full">
                                                            <i class="fas fa-shield-alt fa-fw mr-1"></i>NeoAdmin
                                                        </span>
                                                    @else
                                                        <span class="font-semibold text-gray-800 dark:text-white text-sm">{{ $reply->user->name }}</span>
                                                    @endif
                                                </div>
                                                <span class="text-xs text-gray-500 dark:text-gray-400 flex-shrink-0 ml-2">{{ $reply->created_at->diffForHumans() }}</span>
                                            </div>
                                            <div class="mt-2 flex justify-between items-end text-sm">
                                                <div class="flex-grow">
                                                    {{-- Ini juga pengganti dari partials/comment-content --}}
                                                    <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $reply->content }}</p>
                                                </div>
                                                @if (auth()->check() && auth()->user()->isAdmin())
                                                    <form id="delete-form-{{ $reply->id }}" action="{{ route('comments.destroy', $reply->id) }}" method="POST" class="ml-4 flex-shrink-0">
                                                        @csrf
                                                        @method('DELETE')
                                                         <button type="button" data-form-id="delete-form-{{ $reply->id }}" class="delete-comment-btn text-gray-400 dark:text-gray-500 text-sm transition-all duration-200 hover:text-red-500 dark:hover:text-red-400 hover:scale-125" title="Delete Reply">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                        No comments yet. Be the first one to comment!
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!--   MODAL KONFIRMASI HAPUS (SEBELUMNYA DARI PARTIAL)    -->
<!-- ======================================================== -->
<div id="deleteConfirmModal" class="fixed inset-0 bg-black bg-opacity-60 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-sm w-full mx-4 transform transition-all scale-95 opacity-0" id="deleteModalContent">
        <div class="p-6 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900 mb-4">
                <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Delete Comment?</h3>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Are you sure you want to delete this comment? This action cannot be undone.</p>
        </div>
        <div class="flex items-center justify-center gap-4 p-4 border-t border-gray-200 dark:border-gray-700">
            <button id="cancelDeleteBtn" class="flex-1 bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                Cancel
            </button>
            <button id="confirmDeleteBtn" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                Yes, Delete
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- ======================================================== -->
<!--      SEMUA SCRIPT KOMENTAR (SEBELUMNYA DARI PARTIAL)     -->
<!-- ======================================================== -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fungsi untuk tombol Reply
    document.querySelectorAll('.reply-btn').forEach(button => {
        button.addEventListener('click', function() {
            const commentId = this.dataset.commentId;
            const username = this.dataset.username;
            const replyForm = document.getElementById(`reply-form-${commentId}`);
            const textarea = replyForm.querySelector('textarea');
            const isHidden = replyForm.style.display === 'none';
            replyForm.style.display = isHidden ? 'block' : 'none';
            if (isHidden) {
                textarea.value = `@${username} `;
                textarea.focus();
            }
        });
    });

    @auth
    // Fungsi untuk tombol Like
    document.body.addEventListener('click', function(e) {
        if (e.target.closest('.like-btn')) {
            e.preventDefault();
            const button = e.target.closest('.like-btn');
            const commentId = button.dataset.commentId;
            const likeCountSpan = document.getElementById(`like-count-${commentId}`);
            const likeIcon = button.querySelector('i');
            fetch(`/comments/${commentId}/like`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    likeCountSpan.textContent = data.likes_count;
                    if (data.liked) {
                        likeIcon.classList.remove('far');
                        likeIcon.classList.add('fas', 'text-red-500');
                    } else {
                        likeIcon.classList.remove('fas', 'text-red-500');
                        likeIcon.classList.add('far');
                    }
                }
            })
            .catch(error => console.error('Error liking comment:', error));
        }
    });
    @endauth

    // Fungsi untuk Modal Hapus Komentar
    const deleteModal = document.getElementById('deleteConfirmModal');
    if (deleteModal) {
        const deleteModalContent = document.getElementById('deleteModalContent');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
        let formToSubmit = null;

        const showModal = () => {
            deleteModal.classList.remove('hidden');
            setTimeout(() => {
                deleteModalContent.classList.remove('scale-95', 'opacity-0');
                deleteModalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        };

        const hideModal = () => {
            deleteModalContent.classList.add('scale-95', 'opacity-0');
            deleteModalContent.classList.remove('scale-100', 'opacity-100');
            setTimeout(() => {
                deleteModal.classList.add('hidden');
            }, 200);
        };

        document.querySelectorAll('.delete-comment-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                formToSubmit = document.getElementById(this.dataset.formId);
                if (formToSubmit) {
                    showModal();
                }
            });
        });

        confirmDeleteBtn.addEventListener('click', function() {
            if (formToSubmit) {
                formToSubmit.submit();
            }
            hideModal();
        });

        cancelDeleteBtn.addEventListener('click', hideModal);
        deleteModal.addEventListener('click', function(e) {
            if (e.target === deleteModal) {
                hideModal();
            }
        });
    }
});
</script>
@endpush