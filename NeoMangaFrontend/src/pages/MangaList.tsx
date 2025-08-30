import React, { useState, useEffect, useMemo } from 'react';
import { useSearchParams } from 'react-router-dom';
import type { MangaItem, Genre } from '../types/manga';
import apiRoutes from '../routes/route';
import MangaCard from '../components/MangaCard';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faSpinner } from '@fortawesome/free-solid-svg-icons';

const MangaListPage: React.FC = () => {
  const [mangas, setMangas] = useState<MangaItem[]>([]);
  const [genres, setGenres] = useState<Genre[]>([]);
  const [searchParams, setSearchParams] = useSearchParams();
  const [initialLoading, setInitialLoading] = useState<boolean>(true);
  const [pageLoading, setPageLoading] = useState<boolean>(false);
  const [isGenreModalOpen, setIsGenreModalOpen] = useState<boolean>(false);
  const [currentPage, setCurrentPage] = useState(1);
  const [lastPage, setLastPage] = useState(1);

  const filters = useMemo(() => ({
    q: searchParams.get('q') || '',
    genre: searchParams.getAll('genre[]').map(Number),
    status: searchParams.get('status') || '',
    type: searchParams.get('type') || '',
    order: searchParams.get('order') || 'default',
  }), [searchParams]);

  const [tempSelectedGenres, setTempSelectedGenres] = useState<number[]>([]);

  useEffect(() => {
    const fetchMangaList = async () => {
      const page = parseInt(searchParams.get('page') || '1', 10);
      if (page === 1) {
        setInitialLoading(true);
      } else {
        setPageLoading(true);
      }

      try {
        const response = await apiRoutes.get(`/manga-list?${searchParams.toString()}`);
        setMangas(response.data.mangas?.data || []);
        setCurrentPage(response.data.mangas?.current_page || 1);
        setLastPage(response.data.mangas?.last_page || 1);
        if (genres.length === 0) {
          setGenres(response.data.genres || []);
        }
      } catch (error) {
        console.error("Failed to fetch manga list", error);
        setMangas([]); 
      } finally {
        setInitialLoading(false);
        setPageLoading(false);
      }
    };
    
    fetchMangaList();
    if (parseInt(searchParams.get('page') || '1', 10) > 1) {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }
  }, [searchParams, genres.length]);

  const handleFilterUpdate = (newParams: Record<string, string | string[]>) => {
    const params = new URLSearchParams(searchParams);
    Object.keys(newParams).forEach(key => {
      params.delete(key);
      params.delete(`${key}[]`);
      const value = newParams[key];
      if (Array.isArray(value)) {
        value.forEach(v => params.append(`${key}[]`, String(v)));
      } else if (value) {
        params.set(key, value);
      }
    });
    params.set('page', '1');
    setSearchParams(params);
  };
  
  const handlePageChange = (newPage: number) => {
    const params = new URLSearchParams(searchParams);
    params.set('page', String(newPage));
    setSearchParams(params);
  };

  const handleOpenGenreModal = () => {
    setTempSelectedGenres(filters.genre);
    setIsGenreModalOpen(true);
  };
  
  const handleApplyGenres = () => {
    handleFilterUpdate({ genre: tempSelectedGenres.map(String), q: '' });
    setIsGenreModalOpen(false);
  };

  const pageTitle = filters.q ? `Hasil untuk "${filters.q}"` : "Daftar Manga";

  return (
    <div className="container mx-auto px-4 py-8 md:px-6 md:py-10">
      <h1 className="text-2xl md:text-3xl font-bold mb-6 text-gray-900 dark:text-gray-100">{pageTitle}</h1>
      <div className="p-4 mb-8 rounded-lg shadow-sm bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
        <div className="grid grid-cols-2 lg:grid-cols-4 gap-6">
          <div>
            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Genre</label>
            <button type="button" onClick={handleOpenGenreModal} className="w-full h-10 flex items-center justify-between text-left px-3 py-2 text-base border rounded-md shadow-sm bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600">
              <span className="truncate text-gray-900 dark:text-gray-100">
                {filters.genre.length > 0 ? `${filters.genre.length} genre selected` : 'Pilih Genre'}
              </span>
              <svg className="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fillRule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clipRule="evenodd" /></svg>
            </button>
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
            <select name="status" value={filters.status} onChange={(e) => handleFilterUpdate({ status: e.target.value, q: '' })} className="w-full h-10 pl-3 pr-10 py-2 text-base border rounded-md shadow-sm bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600">
              <option value="">All</option>
              <option value="ongoing">Ongoing</option>
              <option value="completed">Completed</option>
            </select>
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
            <select name="type" value={filters.type} onChange={(e) => handleFilterUpdate({ type: e.target.value, q: '' })} className="w-full h-10 pl-3 pr-10 py-2 text-base border rounded-md shadow-sm bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600">
              <option value="">All</option>
              <option value="manga">Manga</option>
              <option value="manhwa">Manhwa</option>
              <option value="manhua">Manhua</option>
            </select>
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Order</label>
            <select name="order" value={filters.order} onChange={(e) => handleFilterUpdate({ order: e.target.value })} className="w-full h-10 pl-3 pr-10 py-2 text-base border rounded-md shadow-sm bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600">
              <option value="default">Default</option>
              <option value="updated">Updated</option>
              <option value="newest">Added</option>
              <option value="popularity">Popularity</option>
              <option value="rating">Rating</option>
              <option value="z-a">Z-A</option>
              <option value="a-z">A-Z</option>
            </select>
          </div>
        </div>
      </div>

      {isGenreModalOpen && (
         <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/60" onClick={() => setIsGenreModalOpen(false)}>
         <div className="w-full max-w-2xl mx-4 bg-white dark:bg-gray-800 rounded-lg shadow-xl flex flex-col" onClick={e => e.stopPropagation()}>
           <div className="p-5 border-b border-gray-200 dark:border-gray-700"><h3 className="text-lg font-medium text-gray-900 dark:text-gray-100">Pilih Genre</h3></div>
           <div className="p-6 max-h-[60vh] overflow-y-auto">
             <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
               {genres.map(genre => (
                 <label key={genre.id} className={`flex items-center space-x-3 p-3 rounded-lg border cursor-pointer ${tempSelectedGenres.includes(genre.id) ? 'bg-blue-50 dark:bg-blue-900/30 border-blue-500' : 'border-gray-300 dark:border-gray-600'}`}>
                   <input type="checkbox" value={genre.id} checked={tempSelectedGenres.includes(genre.id)} onChange={() => setTempSelectedGenres(p => p.includes(genre.id) ? p.filter(id => id !== genre.id) : [...p, genre.id])} className="h-4 w-4 rounded border-gray-300 text-blue-600" />
                   <span className="text-sm font-medium text-gray-800 dark:text-gray-200">{genre.name}</span>
                 </label>
               ))}
             </div>
           </div>
           <div className="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t flex justify-end items-center space-x-3">
             <button type="button" onClick={() => setIsGenreModalOpen(false)} className="px-4 py-2 border rounded-md">Batal</button>
             <button type="button" onClick={handleApplyGenres} className="px-6 py-2 bg-blue-600 text-white rounded-md">Terapkan</button>
           </div>
         </div>
       </div>
      )}

      {initialLoading ? (
        <div className="flex justify-center items-center h-96"><FontAwesomeIcon icon={faSpinner} spin size="3x" /></div>
      ) : mangas.length > 0 ? (
        <>
          <div className="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-8 gap-x-4 gap-y-8">
            {mangas.map(manga => <MangaCard key={manga.id} manga={manga} />)}
          </div>
          <div className="mt-10">
            {lastPage > 1 && (
              <nav className="flex items-center justify-between">
                <button onClick={() => handlePageChange(currentPage - 1)} disabled={currentPage === 1 || pageLoading} className="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md disabled:opacity-50">Previous</button>
                <div className="hidden sm:block text-sm text-gray-700 dark:text-gray-400">Halaman <span>{currentPage}</span> dari <span>{lastPage}</span></div>
                <button onClick={() => handlePageChange(currentPage + 1)} disabled={currentPage === lastPage || pageLoading} className="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md disabled:opacity-50">
                  {pageLoading && <FontAwesomeIcon icon={faSpinner} spin className="mr-2" />}
                  Next
                </button>
              </nav>
            )}
          </div>
        </>
      ) : (
        <div className="text-center py-20 rounded-lg bg-gray-100 dark:bg-gray-800">
          <svg className="w-16 h-16 mx-auto mb-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
          <h3 className="text-xl font-medium mb-2">Tidak Ada Hasil Ditemukan</h3>
          <p>Coba ubah filter pencarian Anda.</p>
        </div>
      )}
    </div>
  );
};

export default MangaListPage;