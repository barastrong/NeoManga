<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'NeoManga'))</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <script src="https://cdn.tailwindcss.com"></script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style type="text/tailwindcss">
        @layer base {
            html {
                font-family: 'Figtree', sans-serif;
            }
        }
        @layer components {
            .btn-icon {
                @apply flex items-center justify-center h-10 w-10 rounded-full text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700/50 transition-all;
            }
            .btn-primary {
                @apply px-4 py-2 rounded-md text-sm font-semibold bg-indigo-600 text-white hover:bg-indigo-700 transition-colors shadow-sm transform hover:-translate-y-0.5;
            }
            .dropdown-item {
                @apply flex items-center space-x-3 w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors;
            }
            .footer-heading {
                @apply text-sm font-semibold uppercase text-gray-500 dark:text-gray-400 mb-4;
            }
            .footer-link {
                @apply text-sm text-gray-400 hover:text-white transition-colors;
            }
            .social-link {
                @apply w-10 h-10 flex items-center justify-center rounded-full bg-gray-700 text-white transition-all duration-300 transform hover:scale-110 hover:-translate-y-1;
            }
        }
    </style>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Figtree', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    
    @stack('styles')
</head>

<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300">
    <div id="app" class="min-h-screen flex flex-col">
        
        <nav x-data="{ mobileMenuOpen: false }" id="navbar" class="bg-white/95 dark:bg-gray-800/95 backdrop-blur-lg shadow-sm border-b border-gray-200 dark:border-gray-700/50 sticky top-0 z-50 transition-all duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    
                    <a href="{{ route('dashboard') }}" class="flex-shrink-0 flex items-center space-x-2">
                        <i class="fa-solid fa-book-journal-whills text-indigo-600 dark:text-indigo-400 text-2xl"></i>
                        <span class="text-2xl font-bold text-gray-800 dark:text-white">NeoManga</span>
                    </a>
                    
                    <div class="hidden lg:flex items-center space-x-1">
                        @php
                            $navLinkClasses = "relative inline-flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors duration-200 after:content-[''] after:absolute after:w-0 after:h-0.5 after:bottom-1.5 after:left-1/2 after:-translate-x-1/2 after:bg-indigo-500 after:transition-all after:duration-300";
                            $activeClasses = 'bg-gray-100 dark:bg-gray-700 text-indigo-600 dark:text-indigo-400 after:w-1/2';
                            $inactiveClasses = 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:after:w-1/2';
                        @endphp
                        <a href="{{ route('dashboard') }}"
                        class="{{ $navLinkClasses }} {{ request()->is('/') ? $activeClasses : $inactiveClasses }}">
                        Home
                        </a>
                        <a href="{{ url('/categories') }}" class="{{ $navLinkClasses }} {{ request()->is('categories*') ? $activeClasses : $inactiveClasses }}">Manga List</a>
                        <a href="" class=" {{ $navLinkClasses }} ">History</a>
                        <a href="{{ route('bookmark.index') }}" class="{{ $navLinkClasses }} {{ request()->is('bookmarks*') ? $activeClasses : $inactiveClasses }}">Bookmark</a>
                    </div>
                    
                    <div class="flex items-center space-x-2 sm:space-x-4">
                        
                        <div class="hidden md:block">
                            <div class="relative">
                                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                <input type="search" placeholder="Cari manga..." class="search-input w-36 lg:w-56 bg-gray-100 dark:bg-gray-700/50 text-sm pl-10 pr-4 py-2 rounded-full focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-300">
                            </div>
                        </div>
                        
                        <button id="themeToggle" aria-label="Toggle Theme" class="btn-icon">
                            <i class="fa-solid fa-sun text-lg hidden dark:block"></i>
                            <i class="fa-solid fa-moon text-lg dark:hidden"></i>
                        </button>

                        @auth
                            @if(Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->is_admin))
                                <a href="{{ url('/admin') }}" aria-label="Admin Panel" class="btn-icon bg-red-600 text-white hover:bg-red-700">
                                    <i class="fa-solid fa-user-shield"></i>
                                </a>
                            @endif
                            
                            <div class="relative" x-data="{ dropdownOpen: false }" @click.outside="dropdownOpen = false">
                                <button @click="dropdownOpen = !dropdownOpen" class="transition-transform duration-200 hover:scale-105">
                                    <img class="w-10 h-10 rounded-full object-cover ring-2 ring-offset-2 ring-offset-gray-100 dark:ring-offset-gray-900 ring-indigo-500" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=random&color=fff" alt="Avatar">
                                </button>
                                
                                <div x-show="dropdownOpen" x-transition class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-xl ring-1 ring-black ring-opacity-5 origin-top-right z-50" style="display: none;">
                                    <div class="py-1">
                                        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                                            <p class="font-semibold truncate">{{ auth()->user()->name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ auth()->user()->email }}</p>
                                        </div>
                                        <a href="" class="dropdown-item"><i class="fa-regular fa-user w-5"></i> Profile</a>
                                        <a href="" class="dropdown-item"><i class="fa-solid fa-gear w-5"></i> Settings</a>
                                        <div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-red-600 dark:text-red-500 w-full text-left">
                                                <i class="fa-solid fa-right-from-bracket w-5"></i> Logout
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="hidden md:flex items-center space-x-2">
                                <a href="{{ route('login') }}" class="btn-primary">Login</a>
                            </div>
                        @endguest
                        
                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden btn-icon">
                            <i class="fa-solid fa-bars text-xl"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <div x-show="mobileMenuOpen" x-transition class="lg:hidden border-t border-gray-200 dark:border-gray-700" style="display: none;">
                <div class="p-4 space-y-2">
                    @php
                        $mobileLinkClasses = "flex items-center w-full px-4 py-3 text-base font-medium rounded-lg transition-colors";
                        $mobileActiveClasses = 'bg-indigo-50 dark:bg-gray-700/50 text-indigo-700 dark:text-indigo-400';
                        $mobileInactiveClasses = 'text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700';
                    @endphp
                    <a href="{{ route('dashboard') }}" class="{{ $mobileLinkClasses }} {{ request()->is('/') ? $mobileActiveClasses : $mobileInactiveClasses }}">Home</a>
                    <a href="{{ url('/categories') }}" class="{{ $mobileLinkClasses }} {{ request()->is('categories*') ? $mobileActiveClasses : $mobileInactiveClasses }}">Manga List</a>
                    <a href="{{ route('bookmark.index') }}" class="{{ $mobileLinkClasses }} {{ request()->is('bookmarks*') ? $mobileActiveClasses : $mobileInactiveClasses }}">History</a>
                    <a href="{{ route('bookmark.index') }}" class="{{ $mobileLinkClasses }} {{ request()->is('bookmarks*') ? $mobileActiveClasses : $mobileInactiveClasses }}">Bookmark</a>
                    
                    @guest
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4 flex items-center space-x-3">
                            <a href="{{ route('login') }}" class="flex-1 btn-primary text-center">Login</a>
                        </div>
                    @endguest
                </div>
            </div>
        </nav>

        <main class="flex-grow">
            @yield('content')
        </main>

        <footer class="bg-gray-800 dark:bg-gray-950">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div class="md:col-span-2 lg:col-span-1">
                        <a href="{{ url('/') }}" class="flex items-center space-x-2 text-xl font-bold text-white mb-4">
                            <i class="fa-solid fa-book-journal-whills text-indigo-400"></i>
                            <span>NeoManga</span>
                        </a>
                        <p class="text-sm text-gray-400 leading-relaxed">
                            Platform baca manga online terlengkap dengan update terbaru. Nikmati pengalaman membaca yang nyaman dan modern.
                        </p>
                    </div>
                    
                    <div>
                        <h3 class="footer-heading">Navigasi</h3>
                        <ul class="space-y-2">
                            <li><a href="{{ route('dashboard') }}" class="footer-link">Home</a></li>
                            <li><a href="{{ route('bookmark.index') }}" class="footer-link">Bookmark</a></li>
                            <li><a href="{{ url('/categories') }}" class="footer-link">Kategori</a></li>
                        </ul>
                    </div>
                    
                    <div>
                        <h3 class="footer-heading">Informasi</h3>
                        <ul class="space-y-2">
                            <li><a href="#" class="footer-link">Tentang Kami</a></li>
                            <li><a href="#" class="footer-link">Kebijakan Privasi</a></li>
                            <li><a href="#" class="footer-link">Syarat & Ketentuan</a></li>
                        </ul>
                    </div>
                    
                    <div>
                        <h3 class="footer-heading">Ikuti Kami</h3>
                        <div class="flex space-x-3">
                            <a href="#" class="social-link hover:bg-sky-500"><i class="fa-brands fa-twitter"></i></a>
                            <a href="#" class="social-link hover:bg-blue-600"><i class="fa-brands fa-facebook-f"></i></a>
                            <a href="#" class="social-link hover:bg-pink-600"><i class="fa-brands fa-instagram"></i></a>
                            <a href="#" class="social-link hover:bg-red-600"><i class="fa-brands fa-youtube"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="border-t border-gray-700 mt-8 pt-8 text-center text-sm text-gray-500">
                    <p>© {{ date('Y') }} {{ config('app.name', 'NeoManga') }}. All Rights Reserved.</p>
                </div>
            </div>
        </footer>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const themeToggle = document.getElementById('themeToggle');
            const html = document.documentElement;

            const applyTheme = (theme) => {
                if (theme === 'dark') {
                    html.classList.add('dark');
                } else {
                    html.classList.remove('dark');
                }
            };

            const toggleTheme = () => {
                const newTheme = html.classList.contains('dark') ? 'light' : 'dark';
                localStorage.setItem('theme', newTheme);
                applyTheme(newTheme);
            };

            const savedTheme = localStorage.getItem('theme');
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            
            if (savedTheme) {
                applyTheme(savedTheme);
            } else {
                applyTheme(systemPrefersDark ? 'dark' : 'light');
            }

            if (themeToggle) {
                themeToggle.addEventListener('click', toggleTheme);
            }

            const searchInputs = document.querySelectorAll('.search-input');
            searchInputs.forEach(input => {
                input.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        const searchTerm = input.value.trim();
                        if (searchTerm) {
                            window.location.href = `/search?q=${encodeURIComponent(searchTerm)}`;
                        }
                    }
                });
            });

            let lastScrollTop = 0;
            const navbar = document.getElementById('navbar');
            if (navbar) {
                window.addEventListener('scroll', () => {
                    let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                    if (scrollTop > lastScrollTop && scrollTop > navbar.offsetHeight) {
                        navbar.style.transform = 'translateY(-100%)';
                    } else {
                        navbar.style.transform = 'translateY(0)';
                    }
                    lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
                }, { passive: true });
            }
        });
    </script>
    @stack('scripts')
</body>
</html>