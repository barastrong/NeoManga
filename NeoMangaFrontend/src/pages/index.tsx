import React, { useState, useEffect, useCallback } from 'react';
import type { MangaItem, PaginatedResponse } from '../types/manga';
import apiRoutes from '../routes/route';
import MangaCard from '../components/MangaCard';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faSpinner } from '@fortawesome/free-solid-svg-icons';

const HomePage: React.FC = () => {
  const [latestMangas, setLatestMangas] = useState<MangaItem[]>([]);
  const [popularMangas, setPopularMangas] = useState<MangaItem[]>([]);
  const [currentPage, setCurrentPage] = useState<number>(1);
  const [lastPage, setLastPage] = useState<number>(1);
  const [initialLoading, setInitialLoading] = useState<boolean>(true);
  const [pageLoading, setPageLoading] = useState<boolean>(false);
  const [error, setError] = useState<string | null>(null);

  const fetchHomePageData = useCallback(async (page: number) => {
    if (page > 1) {
      setPageLoading(true);
    } else {
      setInitialLoading(true);
    }
    
    setError(null);

    try {
      const response = await apiRoutes.get<{
        mangas: PaginatedResponse<MangaItem>;
        popularMangas: MangaItem[];
      }>(`/mangas?page=${page}`);

      const mangaData = response.data.mangas;

      setLatestMangas(mangaData.data || []);
      setLastPage(mangaData.last_page || 1);
      
      if (page === 1) {
        setPopularMangas(response.data.popularMangas || []);
      }
    } catch (err) {
      setError(err instanceof Error ? err.message : 'An unexpected error occurred');
    } finally {
      setInitialLoading(false);
      setPageLoading(false);
    }
  }, []);

  useEffect(() => {
    fetchHomePageData(currentPage);
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }, [currentPage, fetchHomePageData]);

  if (initialLoading) {
    return <div className="flex justify-center items-center h-96"><FontAwesomeIcon icon={faSpinner} spin size="3x" /></div>;
  }

  if (error) {
    return <div className="text-center p-10 text-red-500">Error: {error}</div>;
  }

  return (
    <div className="container mx-auto px-4 py-8 md:px-6 md:py-10">
      {popularMangas.length > 0 && (
        <section className="mb-12">
          <h2 className="text-2xl font-bold mb-6 text-slate-900 dark:text-slate-100">Lagi Populer</h2>
          <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-x-4 gap-y-8">
            {popularMangas.map(manga => (
              <MangaCard key={`popular-${manga.id}`} manga={manga} />
            ))}
          </div>
        </section>
      )}

      <section>
        <h2 className="text-2xl font-bold mb-6 text-slate-900 dark:text-slate-100">Update Terbaru</h2>
        {latestMangas.length > 0 ? (
          <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-x-4 gap-y-8">
            {latestMangas.map(manga => (
              <MangaCard key={`latest-${manga.id}`} manga={manga} />
            ))}
          </div>
        ) : (
          <div className="text-center text-slate-500 py-10">Tidak ada manga yang ditemukan.</div>
        )}
        
        {lastPage > 1 && (
          <div className="mt-10 flex items-center justify-between">
            <button
              onClick={() => setCurrentPage(prev => Math.max(1, prev - 1))}
              disabled={currentPage === 1 || pageLoading}
              className="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-md hover:bg-slate-50 dark:hover:bg-slate-700 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Previous
            </button>
            <span className="text-sm text-slate-700 dark:text-slate-400">
              Halaman <span className="font-medium text-slate-900 dark:text-white">{currentPage}</span> dari <span className="font-medium text-slate-900 dark:text-white">{lastPage}</span>
            </span>
            <button
              onClick={() => setCurrentPage(prev => Math.min(lastPage, prev + 1))}
              disabled={currentPage === lastPage || pageLoading}
              className="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-md hover:bg-slate-50 dark:hover:bg-slate-700 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {pageLoading ? <FontAwesomeIcon icon={faSpinner} spin className="mr-2" /> : null}
              Next
            </button>
          </div>
        )}
      </section>
    </div>
  );
};

export default HomePage;