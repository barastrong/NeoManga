import React, {useState, useRef, useEffect }from 'react';
import { Link, useLocation, useNavigate, useSearchParams } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import {
  faBookOpen,
  faMagnifyingGlass,
  faSun,
  faMoon,
  faBars,
  faXmark
} from '@fortawesome/free-solid-svg-icons';

const Navbar: React.FC = () => {
  const [isSearchFocused, setIsSearchFocused] = useState(false);
  const [searchQuery, setSearchQuery] = useState('');
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);
  const [isMobileSearchOpen, setIsMobileSearchOpen] = useState(false);
  const [theme, setTheme] = useState(() => localStorage.getItem('theme') || 'dark');

  const searchInputRef = useRef<HTMLInputElement>(null);
  const mobileSearchInputRef = useRef<HTMLInputElement>(null);
  const location = useLocation();
  const navigate = useNavigate();
  const [searchParams] = useSearchParams();
  const { user, isAuthenticated, logout } = useAuth();

  useEffect(() => {
    if (location.pathname === '/manga-list') {
      setSearchQuery(searchParams.get('q') || '');
    } else {
      setSearchQuery('');
    }
    setIsMobileMenuOpen(false);
    setIsMobileSearchOpen(false);
  }, [location, searchParams]);
  
  useEffect(() => {
    if (isMobileSearchOpen) {
      mobileSearchInputRef.current?.focus();
    }
  }, [isMobileSearchOpen]);

  useEffect(() => {
    if (theme === 'dark') {
      document.documentElement.classList.add('dark');
      document.documentElement.style.colorScheme = 'dark';
    } else {
      document.documentElement.classList.remove('dark');
      document.documentElement.style.colorScheme = 'light';
    }
    localStorage.setItem('theme', theme);
  }, [theme]);
  
  useEffect(() => {
    const handleKeyDown = (event: KeyboardEvent) => {
      if ((event.ctrlKey || event.metaKey) && event.key === 'k') {
        event.preventDefault();
        searchInputRef.current?.focus();
      }
    };
    window.addEventListener('keydown', handleKeyDown);
    return () => {
      window.removeEventListener('keydown', handleKeyDown);
    };
  }, []);

  const toggleTheme = () => {
    setTheme(prevTheme => (prevTheme === 'dark' ? 'light' : 'dark'));
  };

  const handleSearchSubmit = (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    const trimmedQuery = searchQuery.trim();
    if (trimmedQuery) {
      navigate(`/manga-list?q=${encodeURIComponent(trimmedQuery)}`);
    } else {
      navigate('/manga-list');
    }
    searchInputRef.current?.blur();
    mobileSearchInputRef.current?.blur();
    setIsMobileSearchOpen(false);
  };

  const handleLogout = () => {
    logout();
    navigate(location.state?.from?.pathname || '/', { replace: true });
  };

  const handleClearSearch = () => {
    setSearchQuery('');
    const inputToFocus = window.innerWidth < 768 ? mobileSearchInputRef.current : searchInputRef.current;
    inputToFocus?.focus();
  };
  
  const toggleMobileMenu = () => {
    if (isMobileSearchOpen) setIsMobileSearchOpen(false);
    setIsMobileMenuOpen(!isMobileMenuOpen);
  };

  const toggleMobileSearch = () => {
    if (isMobileMenuOpen) setIsMobileMenuOpen(false);
    setIsMobileSearchOpen(!isMobileSearchOpen);
  };

  const navItems = [
    { name: 'Home', path: '/' },
    { name: 'Manga List', path: '/manga-list' },
    { name: 'History', path: '/history' },
    { name: 'Bookmark', path: '/bookmark' },
  ];

  const isActiveRoute = (path: string) => location.pathname === path;

  return (
    <nav className="sticky top-0 z-50 border-b border-slate-300/10 bg-slate-900/75 backdrop-blur-xl shadow-lg">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex items-center justify-between h-16">
          <div className={`flex items-center space-x-3 transition-opacity duration-300 ${isMobileSearchOpen ? 'opacity-0 pointer-events-none' : 'opacity-100'}`}>
            <Link to="/" className="flex items-center space-x-3 group">
              <div className="relative">
                <div className="w-9 h-9 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center shadow-lg group-hover:shadow-indigo-500/30 transition-all duration-300 group-hover:scale-105 group-hover:-rotate-6">
                  <FontAwesomeIcon icon={faBookOpen} className="w-5 h-5 text-white" />
                </div>
              </div>
              <div className="text-xl font-bold">
                <span className="text-white">Neo</span>
                <span className="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400">Manga</span>
              </div>
            </Link>
          </div>
          
          <div className="hidden md:flex items-center space-x-1">
            {navItems.map((item) => (
              <Link key={item.name} to={item.path} className={`relative px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 group ${isActiveRoute(item.path) ? 'text-white' : 'text-gray-300 hover:text-white'}`}>
                {item.name}
                <span className={`absolute bottom-0 left-1/2 -translate-x-1/2 h-0.5 bg-indigo-400 rounded-full transition-all duration-300 ${isActiveRoute(item.path) ? 'w-1/2' : 'w-0 group-hover:w-1/3'}`}></span>
              </Link>
            ))}
          </div>
          
          <div className={`flex items-center space-x-2 sm:space-x-4 transition-opacity duration-300 ${isMobileSearchOpen ? 'opacity-0 pointer-events-none' : 'opacity-100'}`}>
            <form onSubmit={handleSearchSubmit} className="relative hidden md:block">
              <div className="relative">
                <input
                  ref={searchInputRef} type="text" placeholder="Search manga..." value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  onFocus={() => setIsSearchFocused(true)} onBlur={() => setIsSearchFocused(false)}
                  className="w-48 md:w-56 bg-slate-800 border-2 text-white placeholder-gray-500 px-4 py-2 pl-10 rounded-full text-sm focus:outline-none transition-all duration-300 ease-in-out focus:w-72 focus:border-indigo-500 focus:bg-slate-700/80 focus:shadow-lg focus:shadow-indigo-500/20 border-slate-700 hover:border-slate-600"
                />
                <div className="absolute left-3.5 top-1/2 transform -translate-y-1/2 pointer-events-none">
                  <FontAwesomeIcon icon={faMagnifyingGlass} className={`w-4 h-4 transition-colors duration-200 ${isSearchFocused ? 'text-indigo-400' : 'text-gray-400'}`} />
                </div>
                {searchQuery && (
                  <button type="button" onClick={handleClearSearch} className="absolute right-3 top-1/2 transform -translate-y-1/2 p-1 text-gray-500 hover:text-white transition-colors">
                    <FontAwesomeIcon icon={faXmark} className="w-4 h-4" />
                  </button>
                )}
                {!isSearchFocused && !searchQuery && (
                  <div className="absolute right-3 top-1/2 transform -translate-y-1/2 text-xs text-gray-500 bg-slate-700/50 px-1.5 py-0.5 rounded-md border border-slate-600 pointer-events-none">
                    Ctrl+K
                  </div>
                )}
              </div>
            </form>
            
            <button title="Search" onClick={toggleMobileSearch} className="p-2 md:hidden text-gray-400 hover:text-white hover:bg-slate-700/50 rounded-full transition-colors duration-200">
              <FontAwesomeIcon icon={faMagnifyingGlass} className="w-5 h-5" />
            </button>
            
            <button title="Toggle Theme" onClick={toggleTheme} className="p-2 text-gray-400 hover:text-white hover:bg-slate-700/50 rounded-full transition-all duration-200 group">
              <FontAwesomeIcon icon={theme === 'dark' ? faSun : faMoon} className="w-5 h-5 group-hover:scale-110 group-hover:text-yellow-300 dark:group-hover:text-yellow-300 transition-all duration-200" />
            </button>
            
            <div className="hidden sm:flex items-center space-x-3">
              {isAuthenticated ? (
                <>
                  <span className="text-white text-sm font-medium whitespace-nowrap">Hi, {user?.name}</span>
                  <button onClick={handleLogout} className="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-full text-sm font-medium transition-all duration-200 shadow-md hover:shadow-lg hover:shadow-red-500/25 transform hover:scale-105">
                    Logout
                  </button>
                </>
              ) : (
                <Link to="/login" state={{ from: location }} className="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-full text-sm font-medium transition-all duration-200 shadow-md hover:shadow-lg hover:shadow-indigo-500/25 transform hover:scale-105">
                  Login
                </Link>
              )}
            </div>
            
            <button onClick={toggleMobileMenu} className="md:hidden p-2 text-gray-400 hover:text-white hover:bg-slate-700/50 rounded-lg transition-all duration-200">
              <span className="sr-only">Open main menu</span>
              <FontAwesomeIcon icon={isMobileMenuOpen ? faXmark : faBars} className="w-6 h-6" />
            </button>
          </div>

          <div className={`absolute top-0 left-0 w-full h-16 flex items-center px-4 bg-slate-900 transition-opacity duration-300 md:hidden ${isMobileSearchOpen ? 'opacity-100' : 'opacity-0 pointer-events-none'}`}>
            <form onSubmit={handleSearchSubmit} className="relative w-full">
              <input
                ref={mobileSearchInputRef} type="text" placeholder="Search manga..." value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                className="w-full bg-slate-800 border-2 text-white placeholder-gray-500 px-4 py-3 pl-10 rounded-lg text-base focus:outline-none transition-colors duration-300 focus:border-indigo-500 focus:bg-slate-700/80 border-slate-700"
              />
              <div className="absolute left-3.5 top-1/2 transform -translate-y-1/2 pointer-events-none">
                <FontAwesomeIcon icon={faMagnifyingGlass} className="w-4 h-4 text-gray-400" />
              </div>
              {searchQuery && (
                <button type="button" onClick={handleClearSearch} className="absolute right-12 top-1/2 transform -translate-y-1/2 p-1 text-gray-500 hover:text-white">
                  <FontAwesomeIcon icon={faXmark} className="w-4 h-4" />
                </button>
              )}
            </form>
            <button onClick={toggleMobileSearch} className="p-2 ml-2 text-gray-400 hover:text-white">
              <FontAwesomeIcon icon={faXmark} className="w-6 h-6" />
            </button>
          </div>
        </div>
      </div>
      
      <div className={`transition-all duration-300 ease-in-out md:hidden overflow-hidden ${isMobileMenuOpen ? 'max-h-screen' : 'max-h-0'}`}>
        <div className="border-t border-slate-700/50 bg-slate-900/95 p-4 space-y-2">
          {navItems.map((item) => (
            <Link key={item.name} to={item.path} className={`block px-4 py-3 rounded-lg text-base font-medium transition-all duration-200 ${isActiveRoute(item.path) ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:text-white hover:bg-slate-700/50'}`}>
              {item.name}
            </Link>
          ))}
          <div className="pt-4 mt-4 border-t border-slate-700/50 sm:hidden">
            {isAuthenticated ? (
              <div className="flex flex-col items-center space-y-4">
                <span className="text-white text-base font-medium">Hi, {user?.name}</span>
                <button onClick={handleLogout} className="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-3 rounded-lg text-base font-medium transition-all duration-200">
                  Logout
                </button>
              </div>
            ) : (
              <Link to="/login" state={{ from: location }} className="block w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-3 rounded-lg text-base font-medium transition-all duration-200">
                Login
              </Link>
            )}
          </div>
        </div>
      </div>
    </nav>
  );
};

export default Navbar;