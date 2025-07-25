@extends('layouts.app')

@push('styles')
<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 8px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #c5c5c5;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
    .dark .custom-scrollbar::-webkit-scrollbar-track {
        background: #2d3748;
    }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #4a5568;
    }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #718096;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 mb-8 shadow-lg border border-gray-200 dark:border-gray-700">
            <div class="flex flex-col md:flex-row gap-6">
                <div class="flex-shrink-0">
                    <img src="{{ $manga->cover_image ? asset('storage/' . $manga->cover_image) : asset('images/no-image.png') }}" alt="{{ $manga->title }}" class="w-48 h-64 object-cover rounded-lg shadow-lg">
                    @auth
                        <button id="bookmarkBtn" data-manga-id="{{ $manga->id }}" class="w-full mt-4 font-bold py-2 px-4 rounded-lg transition duration-200 text-white {{ $isBookmarked ? 'bg-green-600 hover:bg-green-700' : 'bg-blue-600 hover:bg-blue-700' }}">
                            <i class="fas fa-bookmark mr-2"></i>
                            <span id="bookmarkText">{{ $isBookmarked ? 'Remove Bookmark' : 'Add Bookmark' }}</span>
                        </button>
                    @else
                        <button class="js-login-prompt w-full mt-4 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                            <i class="fas fa-bookmark mr-2"></i>
                            Add to Bookmark
                        </button>
                    @endauth
                    <div class="mt-4 text-center">
                        <div class="text-sm mb-2 text-gray-700 dark:text-gray-300">
                            Followed by <span id="followersCount" class="font-semibold text-blue-600">{{ $manga->followers_count }}</span> people
                        </div>
                        <div class="flex justify-center space-x-1">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star text-yellow-400"></i>
                            @endfor
                        </div>
                    </div>
                </div>
                <div class="flex-1">
                    <h1 class="text-3xl font-bold mb-2 text-gray-800 dark:text-white">{{ $manga->title }}</h1>
                    <h2 class="text-lg text-gray-600 dark:text-gray-400 mb-4 italic">{{ $manga->alternative_title }}</h2>
                    <p class="mb-6 leading-relaxed text-gray-700 dark:text-gray-300">
                        {{ $manga->description }}
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <div class="flex"><span class="w-20 text-gray-600 dark:text-gray-400 font-medium">Status:</span><span class="capitalize text-gray-800 dark:text-gray-200">{{ $manga->status }}</span></div>
                            <div class="flex"><span class="w-20 text-gray-600 dark:text-gray-400 font-medium">Type:</span><span class="capitalize text-gray-800 dark:text-gray-200">{{ $manga->type }}</span></div>
                            <div class="flex"><span class="w-20 text-gray-600 dark:text-gray-400 font-medium">Released:</span><span class="text-gray-800 dark:text-gray-200">{{ $manga->created_at->format('Y') }}</span></div>
                        </div>
                        <div class="space-y-2">
                            <div class="flex"><span class="w-20 text-gray-600 dark:text-gray-400 font-medium">Author:</span><span class="text-gray-800 dark:text-gray-200">{{ $manga->author }}</span></div>
                            <div class="flex"><span class="w-20 text-gray-600 dark:text-gray-400 font-medium">Artist:</span><span class="text-gray-800 dark:text-gray-200">{{ $manga->artist ?? 'N/A' }}</span></div>
                            <div class="flex"><span class="w-20 text-gray-600 dark:text-gray-400 font-medium">Posted By:</span><span class="text-gray-800 dark:text-gray-200">{{ $manga->user->name }}</span></div>
                        </div>
                    </div>
                    <div class="mt-4 space-y-2">
                        <div class="flex"><span class="w-24 text-gray-600 dark:text-gray-400 font-medium">Posted On:</span><span class="text-gray-800 dark:text-gray-200">{{ $manga->created_at->format('d M Y') }}</span></div>
                        <div class="flex"><span class="w-24 text-gray-600 dark:text-gray-400 font-medium">Updated On:</span><span class="text-gray-800 dark:text-gray-200">{{ $manga->updated_at->format('d M Y') }}</span></div>
                    </div>
                    <div class="mt-6">
                        <div class="flex flex-wrap gap-2">
                            @foreach($manga->genres as $genre)
                                <span class="bg-blue-600 hover:bg-blue-700 px-3 py-1 rounded-full text-sm text-white transition-colors duration-200">{{ $genre->name }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @auth
            @if($userHistories->isNotEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 mb-8 shadow-lg border border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white">Latest reading</h2>
                    <form action="{{ route('history.resetForManga', $manga->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to reset the reading history for this manga?');">
                        @csrf
                        <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white text-xs font-semibold py-1 px-3 rounded-md transition-colors duration-200">
                            Reset history
                        </button>
                    </form>
                </div>
                <div class="max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                    <div class="space-y-3">
                        @foreach($userHistories as $history)
                        <div class="flex justify-between items-center border border-gray-300 p-3 rounded-lg">
                            <a href="{{ route('chapter.show', $history->chapter->slug) }}" class="font-medium text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-500 transition-colors">
                                Chapter {{ $history->chapter->number }}
                            </a>
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $history->updated_at->format('d M Y, H:i') }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        @endauth

        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 mb-8 shadow-lg border border-gray-200 dark:border-gray-700">
            <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white">Chapter {{ $manga->title }}</h2>
            @if($chapters->count() > 0)
                <div class="max-h-[450px] overflow-y-auto pr-2 custom-scrollbar">
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-2">
                        @foreach($chapters->sortByDesc('number') as $chapter)
                            @if($chapter->status == 'published' || $chapter->status == 'fixed')
                                <div class="bg-gray-50 hover:bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 rounded-lg p-2.5 transition duration-200 border border-gray-300 dark:border-gray-600 hover:border-blue-400 dark:hover:border-blue-500">
                                    <a href="{{ route('chapter.show', $chapter->slug) }}" class="block hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                        <div class="flex items-center justify-between mb-1">
                                            <div class="text-sm font-medium {{ in_array($chapter->id, $readChapters) ? 'text-blue-600 dark:text-blue-600' : 'text-gray-800 dark:text-gray-200' }}">Ch. {{ $chapter->number }}</div>
                                            @if($chapter->status == 'fixed')
                                                <span class="text-xs bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 px-1.5 py-0.5 rounded-md">Fixed</span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $chapter->created_at->format('d M Y') }}</div>
                                    </a>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @else
                <div class="text-center py-12">
                    <div class="text-lg text-gray-600 dark:text-gray-400">No chapters available yet</div>
                    <div class="text-sm mt-2 text-gray-500 dark:text-gray-500">Check back later for updates</div>
                </div>
            @endif
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-lg border border-gray-200 dark:border-gray-700">
            <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white">Comments ({{ $manga->comments->count() }})</h2>
            @auth
                <form action="{{ route('comments.store') }}" method="POST" class="mb-8">
                    @csrf
                    <input type="hidden" name="manga_id" value="{{ $manga->id }}">
                    <div class="flex items-start space-x-4">
                        <img class="w-10 h-10 rounded-full object-cover" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=random&color=fff" alt="Avatar">
                        <div class="flex-1">
                            <textarea name="content" rows="3" class="w-full p-3 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-red-500 focus:border-red-500 transition" placeholder="Add a public comment..." required></textarea>
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
                        <button class=" mt-4 bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                            Login to Comment
                        </button>
                    </a>
                </div>
            @endauth

            <div class="space-y-6">
                @forelse ($manga->comments->whereNull('parent_id')->sortByDesc('created_at') as $comment)
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
                                    <div class="flex-grow text-base text-gray-800 dark:text-gray-200">
                                        @include('partials.comment-content', ['comment' => $comment])
                                    </div>
                                    @if (auth()->check() && auth()->user()->isAdmin())
                                        <form id="delete-form-{{ $comment->id }}" action="{{ route('comments.destroy', $comment->id) }}" method="POST" " class="ml-4 flex-shrink-0">
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
                                    <div class="text-right mt-2">
                                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-1 px-3 rounded-lg">Post Reply</button>
                                    </div>
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
                                                        <span class="font-semibold text-red-500 dark:text-red-400">{{ $reply->user->name }}</span>
                                                        <span class="bg-red-500 text-white text-xs font-semibold px-2 py-0.5 rounded-full">
                                                            <i class="fas fa-shield-alt fa-fw mr-1"></i>NeoAdmin
                                                        </span>
                                                    @else
                                                        <span class="font-semibold text-gray-800 dark:text-white">{{ $reply->user->name }}</span>
                                                    @endif
                                                </div>
                                                <span class="text-xs text-gray-500 dark:text-gray-400 flex-shrink-0 ml-2">{{ $reply->created_at->diffForHumans() }}</span>
                                            </div>
                                            <div class="mt-2 flex justify-between items-end">
                                                <div class="flex-grow text-base text-gray-800 dark:text-gray-200">
                                                    @include('partials.comment-content', ['comment' => $reply])
                                                </div>
                                                @if (auth()->check() && auth()->user()->isAdmin())
                                                    <form id="delete-form-{{ $reply->id }}" action="{{ route('comments.destroy', $reply->id) }}" method="POST"  class="ml-4 flex-shrink-0">
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

@guest
<div id="loginModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4 transform transition-all">
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                <i class="fas fa-bookmark text-red-600 mr-2"></i> Login Required
            </h3>
            <button id="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-6">
            <div class="text-center mb-6">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 dark:bg-red-900 mb-4">
                    <i class="fas fa-heart text-red-600 dark:text-red-400 text-2xl"></i>
                </div>
                <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Save Your Favorite Manga!</h4>
                <p class="text-gray-600 dark:text-gray-400 text-sm">Join our community to bookmark your favorite manga, track your reading progress, and never miss an update!</p>
            </div>
            <div class="space-y-3">
                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400"><i class="fas fa-check-circle text-green-500 mr-3"></i><span>Bookmark unlimited manga</span></div>
                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400"><i class="fas fa-check-circle text-green-500 mr-3"></i><span>Get notified about new chapters</span></div>
                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400"><i class="fas fa-check-circle text-green-500 mr-3"></i><span>Track your reading history</span></div>
                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400"><i class="fas fa-check-circle text-green-500 mr-3"></i><span>Join the manga community</span></div>
            </div>
        </div>
        <div class="flex flex-col sm:flex-row gap-3 p-6 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('login') }}" class="flex-1 bg-red-600 hover:bg-red-700 text-white text-center font-medium py-3 px-4 rounded-lg transition duration-200"><i class="fas fa-sign-in-alt mr-2"></i>Login Now</a>
            <a href="{{ route('register') }}" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white text-center font-medium py-3 px-4 rounded-lg transition duration-200"><i class="fas fa-user-plus mr-2"></i>Sign Up</a>
        </div>
        <div class="px-6 pb-6">
            <p class="text-xs text-center text-gray-500 dark:text-gray-400">It's free and takes less than a minute!</p>
        </div>
    </div>
</div>
@endguest
@endsection

@push('scripts')
<script>
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

    document.addEventListener('DOMContentLoaded', function() {
        @auth
        const bookmarkBtn = document.getElementById('bookmarkBtn');
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
                        const bookmarkText = document.getElementById('bookmarkText');
                        const followersCount = document.getElementById('followersCount');
                        if (data.is_bookmarked) {
                            this.className = 'w-full mt-4 font-bold py-2 px-4 rounded-lg transition duration-200 text-white bg-green-600 hover:bg-green-700';
                            bookmarkText.textContent = 'Remove Bookmark';
                        } else {
                            this.className = 'w-full mt-4 font-bold py-2 px-4 rounded-lg transition duration-200 text-white bg-blue-600 hover:bg-blue-700';
                            bookmarkText.textContent = 'Add Bookmark';
                        }
                        followersCount.textContent = data.followers_count;
                    }
                }).catch(error => console.error('Error:', error)).finally(() => {
                    this.disabled = false;
                });
            });
        }

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

        const deleteModal = document.getElementById('deleteConfirmModal');
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

        @guest
        const loginModal = document.getElementById('loginModal');
        const closeModal = document.getElementById('closeModal');
        const loginPromptTriggers = document.querySelectorAll('.js-login-prompt');

        const showLoginModal = () => {
            loginModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        };

        const hideLoginModal = () => {
            loginModal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        };

        loginPromptTriggers.forEach(trigger => {
            trigger.addEventListener('click', showLoginModal);
        });

        if (closeModal) {
            closeModal.addEventListener('click', hideLoginModal);
        }

        loginModal.addEventListener('click', (e) => {
            if (e.target === loginModal) {
                hideLoginModal();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !loginModal.classList.contains('hidden')) {
                hideLoginModal();
            }
        });
        @endguest
    });
</script>
@endpush