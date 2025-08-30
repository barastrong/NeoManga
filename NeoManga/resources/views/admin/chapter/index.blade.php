@extends('layouts.adminSidebar')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.manga.index') }}" class="inline-flex items-center text-sm font-semibold text-slate-600 hover:text-indigo-600 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" /></svg>
            Kembali ke Daftar Manga
        </a>
        <h1 class="text-3xl font-bold text-slate-800 mt-2">Chapters untuk: <span class="text-indigo-600">{{ $manga->title }}</span></h1>
    </div>

    <div class="flex justify-end mb-5">
        <a href="{{ route('admin.manga.chapters.create', $manga) }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-lg shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
            Tambah Chapter Baru
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded-lg relative mb-5" role="alert">
            <strong class="font-bold">Sukses!</strong> <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white shadow-lg rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr class="bg-slate-100 text-slate-600 uppercase text-sm font-semibold">
                        <th class="py-3 px-6 text-left">Chapter</th>
                        <th class="py-3 px-6 text-center">Jumlah Gambar</th>
                        <th class="py-3 px-6 text-center">Status</th>
                        <th class="py-3 px-6 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @forelse ($chapters as $chapter)
                        <tr class="border-b border-gray-200 hover:bg-slate-50 transition">
                            <td class="py-4 px-6 font-semibold">Chapter {{ $chapter->number }}</td>
                            <td class="py-4 px-6 text-center">{{ $chapter->image_count }}</td>
                            <td class="py-4 px-6 text-center">
                                @php $statusClass = $chapter->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; @endphp
                                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">{{ ucfirst($chapter->status) }}</span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <div class="flex items-center justify-center space-x-4">
                                    <a href="{{ route('admin.manga.chapters.edit', [$manga, $chapter]) }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">Edit</a>
                                    <form action="{{ route('admin.manga.chapters.destroy', [$manga, $chapter]) }}" method="POST" onsubmit="return confirm('Yakin mau hapus Chapter {{ $chapter->number }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 font-semibold">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 px-6 text-center text-gray-500">
                                <p class="font-semibold">Manga ini belum memiliki chapter.</p>
                                <p class="text-sm mt-1">Silakan klik tombol "Tambah Chapter Baru" untuk memulai.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-6">{{ $chapters->links() }}</div>
@endsection