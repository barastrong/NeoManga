{{-- GANTI SELURUH ISI FILE INI: resources/views/partials/comment-content.blade.php (Versi Final) --}}

@php
    // Mulai dengan konten yang sudah di-escape untuk keamanan
    $processedContent = e($comment->content);

    // --- LANGKAH 1: PROSES MENTION UNTUK ADMIN (NAMA DENGAN SPASI) ---

    // Ambil daftar admin dari cache, diurutkan dari nama terpanjang ke terpendek
    $adminUsers = Cache::remember('admin_users_for_mentions', 60, function () {
        $admins = \App\Models\User::where('role', 'admin')->get()->toArray();
        usort($admins, fn($a, $b) => strlen($b['name']) <=> strlen($a['name']));
        return $admins;
    });

    // Loop HANYA untuk admin dan ganti mention mereka
    foreach ($adminUsers as $admin) {
        $mentionString = e('@' . $admin['name']);
        // Beri warna berbeda untuk mention admin agar lebih menonjol
        $link = '<a href="'. route('user.profile', $admin['name']) .'" class="font-bold text-red-500 hover:underline">' . $mentionString . '</a>';
        $processedContent = str_replace($mentionString, $link, $processedContent);
    }

    // --- LANGKAH 2: PROSES MENTION UNTUK USER BIASA (NAMA TANPA SPASI) ---

    // Gunakan Regex yang presisi untuk nama tanpa spasi
    $processedContent = preg_replace_callback(
        // Regex ini mencari @ diikuti oleh karakter non-spasi (\w adalah huruf, angka, underscore)
        '/(?<!\w)@(\w+)/',
        function ($matches) {
            $name = $matches[1]; // Ambil nama tanpa @

            // Cari user biasa dengan nama tersebut
            $user = \App\Models\User::where('name', $name)->where('role', '!=', 'admin')->first();

            // Jika user biasa ditemukan, ganti dengan link
            if ($user) {
                return '<a href="'. route('user.profile', $user->name) .'" class="font-bold text-blue-500 hover:underline">@' . e($name) . '</a>';
            }

            // Jika tidak ditemukan, kembalikan teks aslinya (@namayangtidakada)
            return $matches[0];
        },
        $processedContent // Proses konten yang mungkin sudah berisi link admin
    );

@endphp

{{-- Tampilkan hasil akhir yang sudah diproses --}}
<p class="mt-2 text-gray-700 dark:text-gray-300 break-words">{!! nl2br($processedContent) !!}</p>