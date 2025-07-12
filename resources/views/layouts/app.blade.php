<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'NeoManga') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div id="app" class="min-h-screen transition-colors duration-300 bg-white dark:bg-gray-900">
        
        <nav class="bg-gradient-to-r from-indigo-900 via-purple-900 to-indigo-900 shadow-2xl border-b border-indigo-700/50 backdrop-blur-sm sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">
                    
                    <div class="flex items-center group">
                        <a href="{{ url('/') }}" class="relative text-3xl font-bold text-white hover:text-cyan-300 transition-all duration-300">
                            <span class="absolute -inset-1 bg-gradient-to-r from-cyan-400 to-blue-400 rounded-lg blur opacity-75 group-hover:opacity-100 transition-opacity"></span>
                            <span class="relative px-4 py-2">📚 NeoManga</span>
                        </a>
                    </div>
                    
                    <div class="hidden lg:flex items-center space-x-4">
                        <a href="{{ url('/dashboard') }}" class="nav-link group flex items-center space-x-2 text-gray-200 hover:text-cyan-300 px-4 py-2 rounded-xl text-sm font-medium transition-all duration-300 hover:bg-white/10">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path></svg>
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ url('/manga') }}" class="nav-link group flex items-center space-x-2 text-gray-200 hover:text-cyan-300 px-4 py-2 rounded-xl text-sm font-medium transition-all duration-300 hover:bg-white/10">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            <span>Manga</span>
                        </a>
                        <a href="{{ url('/favorites') }}" class="nav-link group flex items-center space-x-2 text-gray-200 hover:text-cyan-300 px-4 py-2 rounded-xl text-sm font-medium transition-all duration-300 hover:bg-white/10">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                            <span>Favorit</span>
                        </a>
                        <a href="{{ url('/categories') }}" class="nav-link group flex items-center space-x-2 text-gray-200 hover:text-cyan-300 px-4 py-2 rounded-xl text-sm font-medium transition-all duration-300 hover:bg-white/10">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                            <span>Kategori</span>
                        </a>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        
                        <div class="hidden md:block relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input type="text" placeholder="Cari manga..." class="bg-white/10 text-white placeholder-gray-300 pl-10 pr-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-400 focus:bg-white/20 transition-all duration-300 w-64">
                        </div>
                        
                        @auth
                            @if(Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->is_admin))
                                <div class="relative group">
                                    <a href="{{ url('/admin') }}" class="admin-btn flex items-center space-x-2 bg-gradient-to-r from-red-600 to-pink-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:from-red-700 hover:to-pink-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        <span class="hidden sm:block">Admin</span>
                                    </a>
                                </div>
                            @endif
                        @endauth
                        
                        <button id="themeToggle" class="theme-toggle bg-gradient-to-r from-gray-700 to-gray-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:from-gray-600 hover:to-gray-500 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center space-x-2">
                            <span class="text-lg">🌙</span>
                            <span class="hidden sm:block">Dark</span>
                        </button>
                        
                        @guest
                            <div class="hidden md:flex items-center space-x-3">
                                <a href="{{ route('login') }}" class="auth-btn bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                                    Login
                                </a>
                                <a href="{{ route('register') }}" class="auth-btn bg-gradient-to-r from-emerald-600 to-teal-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:from-emerald-700 hover:to-teal-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                                    Register
                                </a>
                            </div>
                        @else
                            <div class="relative group">
                                <button class="user-menu flex items-center space-x-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:from-purple-700 hover:to-indigo-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                                    <img class="w-8 h-8 rounded-full bg-white/20 object-cover" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=random&color=fff" alt="{{ auth()->user()->name }}">
                                    <span class="hidden sm:block">{{ auth()->user()->name }}</span>
                                    <svg class="w-4 h-4 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                
                                <div class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                                    <div class="py-1">
                                        <a href="{{ url('/profile') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors flex items-center space-x-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                            <span>Profile</span>
                                        </a>
                                        <a href="{{ url('/settings') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors flex items-center space-x-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            <span>Settings</span>
                                        </a>
                                        <div class="border-t border-gray-200 dark:border-gray-600"></div>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors flex items-center space-x-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                                <span>Logout</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endguest
                        
                        <button id="mobileMenuButton" class="lg:hidden text-gray-200 hover:text-cyan-300 focus:outline-none p-2 rounded-lg hover:bg-white/10 transition-colors">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                    </div>
                </div>
                
                <div id="mobileMenu" class="lg:hidden hidden border-t border-indigo-700/50">
                    <div class="px-2 pt-2 pb-3 space-y-3 sm:px-3">
                        <div class="md:hidden mb-4">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                                <input type="text" placeholder="Cari manga..." class="bg-white/10 text-white placeholder-gray-300 pl-10 pr-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-400 focus:bg-white/20 transition-all duration-300 w-full">
                            </div>
                        </div>
                        
                        <a href="{{ url('/dashboard') }}" class="mobile-nav-link text-gray-200 hover:text-cyan-300 block px-3 py-2 rounded-md text-base font-medium hover:bg-white/10 transition-colors flex items-center space-x-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path></svg>
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ url('/manga') }}" class="mobile-nav-link text-gray-200 hover:text-cyan-300 block px-3 py-2 rounded-md text-base font-medium hover:bg-white/10 transition-colors flex items-center space-x-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            <span>Manga</span>
                        </a>
                        <a href="{{ url('/favorites') }}" class="mobile-nav-link text-gray-200 hover:text-cyan-300 block px-3 py-2 rounded-md text-base font-medium hover:bg-white/10 transition-colors flex items-center space-x-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                            <span>Favorit</span>
                        </a>
                        <a href="{{ url('/categories') }}" class="mobile-nav-link text-gray-200 hover:text-cyan-300 block px-3 py-2 rounded-md text-base font-medium hover:bg-white/10 transition-colors flex items-center space-x-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                            <span>Kategori</span>
                        </a>
                        
                        @auth
                            @if(Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->is_admin))
                                <a href="{{ url('/admin') }}" class="mobile-nav-link text-gray-200 hover:text-red-300 block px-3 py-2 rounded-md text-base font-medium hover:bg-red-600/20 transition-colors flex items-center space-x-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    <span>Admin Panel</span>
                                </a>
                            @endif
                        @endauth
                        
                        @guest
                            <div class="pt-4 pb-3 flex flex-col space-y-3">
                                <a href="{{ route('login') }}" class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-center px-4 py-2 rounded-md text-sm font-medium hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 shadow-md">
                                    Login
                                </a>
                                <a href="{{ route('register') }}" class="bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-center px-4 py-2 rounded-md text-sm font-medium hover:from-emerald-700 hover:to-teal-700 transition-all duration-300 shadow-md">
                                    Register
                                </a>
                            </div>
                        @else
                            <div class="pt-4 pb-3 border-t border-indigo-700/50 mt-4">
                                <div class="flex items-center space-x-3 px-3 py-2 mb-3">
                                    <img class="w-8 h-8 rounded-full bg-white/20 object-cover" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=random&color=fff" alt="{{ auth()->user()->name }}">
                                    <span class="text-white font-medium">{{ auth()->user()->name }}</span>
                                </div>
                                
                                <div class="space-y-2">
                                    <a href="{{ url('/profile') }}" class="mobile-nav-link text-gray-200 hover:text-cyan-300 block px-3 py-2 rounded-md text-base font-medium hover:bg-white/10 transition-colors flex items-center space-x-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        <span>Profile</span>
                                    </a>
                                    <a href="{{ url('/settings') }}" class="mobile-nav-link text-gray-200 hover:text-cyan-300 block px-3 py-2 rounded-md text-base font-medium hover:bg-white/10 transition-colors flex items-center space-x-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        <span>Settings</span>
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full text-left mobile-nav-link text-red-400 hover:text-red-300 block px-3 py-2 rounded-md text-base font-medium hover:bg-red-600/20 transition-colors flex items-center space-x-3">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                            <span>Logout</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endguest
                    </div>
                </div>
            </div>
        </nav>

        <main class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
            @yield('content')
        </main>

        <footer class="bg-gradient-to-r from-gray-900 via-indigo-900 to-gray-900 text-white mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div class="col-span-1 md:col-span-2">
                        <div class="flex items-center space-x-3 mb-6">
                            <div class="relative">
                                <div class="absolute -inset-1 bg-gradient-to-r from-cyan-400 to-blue-400 rounded-lg blur opacity-75"></div>
                                <div class="relative bg-gradient-to-r from-indigo-900 to-purple-900 px-4 py-2 rounded-lg">
                                    <span class="text-2xl font-bold">📚 NeoManga</span>
                                </div>
                            </div>
                        </div>
                        <p class="text-gray-300 text-sm leading-relaxed mb-6">
                            Platform terbaik untuk membaca manga online dengan koleksi lengkap dan update terbaru. 
                            Nikmati pengalaman membaca yang nyaman dengan fitur-fitur canggih.
                        </p>
                        <div class="flex space-x-4">
                            <a href="#" class="social-link bg-blue-600 hover:bg-blue-700 p-3 rounded-full transition-all duration-300 hover:scale-110">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                            </a>
                            <a href="#" class="social-link bg-indigo-600 hover:bg-indigo-700 p-3 rounded-full transition-all duration-300 hover:scale-110">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/></svg>
                            </a>
                            <a href="#" class="social-link bg-pink-600 hover:bg-pink-700 p-3 rounded-full transition-all duration-300 hover:scale-110">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.219-.359-1.219c0-1.141.663-1.992 1.488-1.992.702 0 1.041.219 1.041 1.219 0 .735-.468 1.83-.711 2.846-.203.854.428 1.55 1.271 1.55 1.524 0 2.693-1.606 2.693-3.925 0-2.056-1.477-3.49-3.597-3.49-2.449 0-3.885 1.837-3.885 3.735 0 .735.281 1.529.632 1.957.07.08.08.151.059.234-.065.271-.211.859-.241.979-.04.162-.129.198-.297.119-1.097-.511-1.785-2.115-1.785-3.406 0-2.766 2.009-5.311 5.791-5.311 3.042 0 5.409 2.169 5.409 5.072 0 3.024-1.908 5.455-4.552 5.455-.888 0-1.724-.462-2.009-1.013l-.548 2.088c-.198.766-.734 1.724-1.093 2.306.824.254 1.696.389 2.596.389 6.624 0 11.99-5.367 11.99-11.987C24.007 5.367 18.641.001 12.017.001z"/></svg>
                            </a>
                            <a href="#" class="social-link bg-red-600 hover:bg-red-700 p-3 rounded-full transition-all duration-300 hover:scale-110">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                            </a>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-semibold mb-6 text-cyan-300">Navigasi</h3>
                        <ul class="space-y-3">
                            <li><a href="{{ url('/') }}" class="footer-link text-gray-300 hover:text-cyan-300 text-sm transition-colors duration-300 flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                <span>Beranda</span>
                            </a></li>
                            <li><a href="{{ url('/manga') }}" class="footer-link text-gray-300 hover:text-cyan-300 text-sm transition-colors duration-300 flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                <span>Koleksi Manga</span>
                            </a></li>
                            <li><a href="{{ url('/categories') }}" class="footer-link text-gray-300 hover:text-cyan-300 text-sm transition-colors duration-300 flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                <span>Kategori</span>
                            </a></li>
                            <li><a href="{{ url('/favorites') }}" class="footer-link text-gray-300 hover:text-cyan-300 text-sm transition-colors duration-300 flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                                <span>Favorit</span>
                            </a></li>
                        </ul>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-semibold mb-6 text-cyan-300">Informasi</h3>
                        <ul class="space-y-3">
                            <li><a href="#" class="footer-link text-gray-300 hover:text-cyan-300 text-sm transition-colors duration-300">Tentang Kami</a></li>
                            <li><a href="#" class="footer-link text-gray-300 hover:text-cyan-300 text-sm transition-colors duration-300">Kebijakan Privasi</a></li>
                            <li><a href="#" class="footer-link text-gray-300 hover:text-cyan-300 text-sm transition-colors duration-300">Syarat & Ketentuan</a></li>
                            <li><a href="#" class="footer-link text-gray-300 hover:text-cyan-300 text-sm transition-colors duration-300">Hubungi Kami</a></li>
                            <li><a href="#" class="footer-link text-gray-300 hover:text-cyan-300 text-sm transition-colors duration-300">FAQ</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="border-t border-gray-700 mt-8 pt-8 flex flex-col md:flex-row justify-between items-center">
                    <div class="flex items-center space-x-4 mb-4 md:mb-0">
                        <p class="text-gray-400 text-sm">© 2024 NeoManga. All rights reserved.</p>
                    </div>
                    <div class="flex items-center space-x-6">
                        <div class="flex items-center space-x-2 text-gray-400 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            <span>Secured by SSL</span>
                        </div>
                        <div class="flex items-center space-x-2 text-gray-400 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>Verified Platform</span>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <script>
        // Mobile menu toggle
        const mobileMenuButton = document.getElementById('mobileMenuButton');
        const mobileMenu = document.getElementById('mobileMenu');
        
        if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }

        // Theme toggle functionality
        const themeToggle = document.getElementById('themeToggle');
        const html = document.documentElement;
        const themeIcon = themeToggle.querySelector('span');
        const themeText = themeToggle.querySelector('span:last-child');
        
        // Check for saved theme preference or default to light
        const currentTheme = localStorage.getItem('theme') || 'light';
        
        if (currentTheme === 'dark') {
            html.classList.add('dark');
            themeIcon.textContent = '☀️';
            if (themeText) themeText.textContent = 'Light';
        }
        
        themeToggle.addEventListener('click', () => {
            html.classList.toggle('dark');
            
            if (html.classList.contains('dark')) {
                localStorage.setItem('theme', 'dark');
                themeIcon.textContent = '☀️';
                if (themeText) themeText.textContent = 'Light';
            } else {
                localStorage.setItem('theme', 'light');
                themeIcon.textContent = '🌙';
                if (themeText) themeText.textContent = 'Dark';
            }
        });

        // Search functionality
        const searchInputs = document.querySelectorAll('input[type="text"]');
        searchInputs.forEach(input => {
            input.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    const searchTerm = input.value.trim();
                    if (searchTerm) {
                        window.location.href = `/search?q=${encodeURIComponent(searchTerm)}`;
                    }
                }
            });
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add loading animation for navigation links
        document.querySelectorAll('a[href^="/"]').forEach(link => {
            link.addEventListener('click', function(e) {
                if (!this.getAttribute('href').startsWith('#')) {
                    const loader = document.createElement('div');
                    loader.className = 'fixed top-0 left-0 w-full h-1 bg-gradient-to-r from-cyan-400 to-blue-500 z-50 animate-pulse';
                    document.body.appendChild(loader);
                    
                    setTimeout(() => {
                        if (loader.parentNode) {
                            loader.parentNode.removeChild(loader);
                        }
                    }, 1000);
                }
            });
        });

        // Add hover effects for buttons
        document.querySelectorAll('.auth-btn, .admin-btn, .theme-toggle').forEach(button => {
            button.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.05)';
            });
            
            button.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
            });
        });

        // Auto-hide mobile menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!mobileMenuButton.contains(e.target) && !mobileMenu.contains(e.target)) {
                mobileMenu.classList.add('hidden');
            }
        });

        // Add scroll effect to navbar
        let lastScrollTop = 0;
        const navbar = document.querySelector('nav');
        
        window.addEventListener('scroll', () => {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            if (scrollTop > lastScrollTop && scrollTop > 100) {
                navbar.style.transform = 'translateY(-100%)';
            } else {
                navbar.style.transform = 'translateY(0)';
            }
            
            lastScrollTop = scrollTop;
        });

        // Initialize tooltips for icons
        const tooltips = [
            { selector: '.theme-toggle', text: 'Toggle Theme' },
            { selector: '.admin-btn', text: 'Admin Panel' },
            { selector: '.user-menu', text: 'User Menu' }
        ];

        tooltips.forEach(tooltip => {
            const elements = document.querySelectorAll(tooltip.selector);
            elements.forEach(element => {
                element.setAttribute('title', tooltip.text);
            });
        });
    </script>

    <style>
        .nav-link {
            position: relative;
            overflow: hidden;
        }
        
        .nav-link::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #06b6d4, #3b82f6);
            transition: width 0.3s ease;
        }
        
        .nav-link:hover::before {
            width: 100%;
        }
        
        .social-link {
            position: relative;
            overflow: hidden;
        }
        
        .social-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .social-link:hover::before {
            left: 100%;
        }
        
        .footer-link {
            position: relative;
            display: inline-block;
        }
        
        .footer-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 1px;
            bottom: -2px;
            left: 0;
            background-color: #06b6d4;
            transition: width 0.3s ease;
        }
        
        .footer-link:hover::after {
            width: 100%;
        }
        
        @keyframes glow {
            0%, 100% { box-shadow: 0 0 10px rgba(6, 182, 212, 0.5); }
            50% { box-shadow: 0 0 20px rgba(6, 182, 212, 0.8); }
        }
        
        .user-menu:hover {
            animation: glow 2s infinite;
        }
        
        nav {
            transition: transform 0.3s ease;
        }
        
        .mobile-nav-link {
            position: relative;
            overflow: hidden;
        }
        
        .mobile-nav-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 0;
            height: 100%;
            background: linear-gradient(90deg, rgba(6, 182, 212, 0.1), rgba(59, 130, 246, 0.1));
            transition: width 0.3s ease;
        }
        
        .mobile-nav-link:hover::before {
            width: 100%;
        }
    </style>
</body>
</html>