<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Manga Site</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-100 font-sans antialiased">

<div x-data="{ sidebarOpen: false }" class="relative min-h-screen lg:flex">

    <!-- Overlay untuk mobile saat sidebar terbuka -->
    <div 
        x-show="sidebarOpen" 
        @click="sidebarOpen = false" 
        class="fixed inset-0 z-20 bg-black opacity-50 transition-opacity lg:hidden"
        x-cloak
    ></div>

    <!-- Sidebar -->
    <aside 
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 z-30 w-64 bg-slate-800 text-white transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0"
    >
        <div class="flex items-center justify-center h-16 bg-slate-900">
            <a href="{{ route('admin.dashboard') }}" class="text-2xl font-bold tracking-wider">AdminPanel</a>
        </div>

        <nav class="mt-8">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-6 py-3 mt-4 duration-200 border-l-4 {{ request()->routeIs('admin.dashboard') ? 'bg-slate-700 border-indigo-500' : 'border-slate-800' }} hover:bg-slate-700 hover:border-indigo-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <span class="ml-4">Dashboard</span>
            </a>
            <a href="{{ route('admin.user.index') }}" class="flex items-center px-6 py-3 mt-4 duration-200 border-l-4 {{ request()->routeIs('admin.user.*') ? 'bg-slate-700 border-indigo-500' : 'border-slate-800' }} hover:bg-slate-700 hover:border-indigo-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M15 21a6 6 0 00-9-5.197m0 0A10.004 10.004 0 0012 10a10.004 10.004 0 00-3-7.303M15 21a9 9 0 00-6-16.147"></path></svg>
                <span class="ml-4">Users</span>
            </a>
            <a href="{{ route('admin.manga.index') }}" class="flex items-center px-6 py-3 mt-4 duration-200 border-l-4 {{ request()->routeIs('admin.manga.*') ? 'bg-slate-700 border-indigo-500' : 'border-slate-800' }} hover:bg-slate-700 hover:border-indigo-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v11.494m-9-5.747h18"></path></svg>
                <span class="ml-4">Manga</span>
            </a>
        </nav>
    </aside>

    <div class="flex-1 flex flex-col">
        <header class="flex justify-between items-center p-4 bg-white border-b border-slate-200">
            <div class="flex items-center">
                <button @click="sidebarOpen = true" class="text-gray-500 focus:outline-none lg:hidden">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </button>
            </div>

            <div x-data="{ dropdownOpen: false }" class="relative">
                <button @click="dropdownOpen = !dropdownOpen" class="relative block h-8 w-8 rounded-full overflow-hidden shadow focus:outline-none">
                    <img class="h-full w-full object-cover" src="https://ui-avatars.com/api/?name=Admin&background=random" alt="Your avatar">
                </button>

                <div x-show="dropdownOpen" @click.away="dropdownOpen = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-xl z-20" x-cloak>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-600 hover:text-white">Profile</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-600 hover:text-white">Settings</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-600 hover:text-white">Logout</a>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-100 p-6">
            <div class="container mx-auto">
                @yield('content')
            </div>
        </main>
    </div>
</div>

</body>
</html>