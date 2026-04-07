import React, { useState, useEffect, useCallback } from 'react';
import { Link } from 'react-router-dom';
import { getBookmarks, removeBookmark } from '../services/BookmarkService';
import { useAuth } from '../context/AuthContext';
import type { Bookmark, PaginatedResponse } from '../types/manga';
import BookmarkCard from '../components/BookmarkCard';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faSpinner } from '@fortawesome/free-solid-svg-icons';

const BookmarkPage: React.FC = () => {
  const { isAuthenticated, isLoading: isAuthLoading } = useAuth();
  const [bookmarkData, setBookmarkData] = useState<PaginatedResponse<Bookmark> | null>(null);
  const [initialLoading, setInitialLoading] = useState<boolean>(true);
  const [pageLoading, setPageLoading] = useState<boolean>(false);
  const [notification, setNotification] = useState<string | null>(null);

  const fetchBookmarks = useCallback(async (page: number = 1) => {
    if (!isAuthenticated) { 
      setInitialLoading(false);
      return; 
    }
    
    if (page === 1) {
      setInitialLoading(true);
    } else {
      setPageLoading(true);
    }

    try {
      const data = await getBookmarks(page);
      setBookmarkData(data); // Mengganti data, bukan menambahkan
    } catch (err) {
      console.error("Failed to fetch bookmarks:", err);
    } finally {
      setInitialLoading(false);
      setPageLoading(false);
    }
  }, [isAuthenticated]);

  useEffect(() => {
    if (!isAuthLoading) {
      fetchBookmarks(1);
    }
  }, [isAuthLoading, fetchBookmarks]);
  
  const handlePageChange = (newPage: number) => {
    if (newPage >= 1 && newPage <= (bookmarkData?.last_page || 1)) {
        fetchBookmarks(newPage);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
  };

  const handleRemoveBookmark = async (bookmarkId: number) => {
    try {
      const response = await removeBookmark(bookmarkId);
      setNotification(response.message);
      setBookmarkData(prevData => {
        if (!prevData) return null;
        return {
          ...prevData,
          data: prevData.data.filter(item => item.id !== bookmarkId),
          total: prevData.total - 1,
        };
      });
    } catch (err) {
      console.error("Failed to remove bookmark:", err);
    } finally {
      setTimeout(() => setNotification(null), 3000);
    }
  };

  if (isAuthLoading || initialLoading) {
    return <div className="flex justify-center items-center h-96"><FontAwesomeIcon icon={faSpinner} spin size="3x" /></div>;
  }

  if (!isAuthenticated) {
    return (
      <div className="container mx-auto px-4 py-8 md:px-6 md:py-10">
        <div className="text-center py-20 border-2 border-dashed border-slate-300 dark:border-slate-700 rounded-lg">
          <svg className="w-16 h-16 mx-auto mb-5 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
          <h3 className="text-xl font-medium mb-2 text-slate-900 dark:text-slate-100">Akses Bookmark Anda</h3>
          <p className="text-slate-500 dark:text-slate-400 mb-6">Silakan login untuk melihat dan mengelola manga yang telah Anda tandai.</p>
          <Link to="/login" className="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-md text-sm transition-colors">
              Login
          </Link>
        </div>
      </div>
    );
  }

  const bookmarks = bookmarkData?.data || [];
  const currentPage = bookmarkData?.current_page || 1;
  const lastPage = bookmarkData?.last_page || 1;

  return (
    <div className="container mx-auto px-4 py-8 md:px-6 md:py-10">
      <div className="flex justify-between items-center mb-6">
        <h1 className="text-2xl font-bold text-gray-900 dark:text-white">Bookmark</h1>
      </div>

      {notification && (
        <div className="bg-green-100 dark:bg-green-500/20 border border-green-400 text-green-700 dark:text-green-300 px-4 py-3 rounded relative mb-6" role="alert">
            <span>{notification}</span>
        </div>
      )}

      {bookmarks.length > 0 ? (
        <>
          <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-x-4 gap-y-8">
            {bookmarks.map(bookmark => (
              <BookmarkCard key={bookmark.id} bookmark={bookmark} onDelete={handleRemoveBookmark} />
            ))}
          </div>

          {lastPage > 1 && (
            <div className="mt-10 flex justify-between items-center">
                <button
                    onClick={() => handlePageChange(currentPage - 1)}
                    disabled={!bookmarkData?.prev_page_url || pageLoading}
                    className="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-md disabled:opacity-50"
                >
                    Previous
                </button>
                <span className="text-sm text-slate-700 dark:text-slate-400">
                    Halaman {currentPage} dari {lastPage}
                </span>
                <button
                    onClick={() => handlePageChange(currentPage + 1)}
                    disabled={!bookmarkData?.next_page_url || pageLoading}
                    className="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-md disabled:opacity-50"
                >
                    {pageLoading && <FontAwesomeIcon icon={faSpinner} spin className="mr-2" />}
                    Next
                </button>
            </div>
          )}
        </>
      ) : (
        <div className="text-center py-20 border-2 border-dashed border-slate-300 dark:border-slate-700 rounded-lg">
            <svg className="w-16 h-16 mx-auto mb-5 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg>
            <h3 className="text-xl font-medium mb-2 text-slate-900 dark:text-slate-100">Bookmark Anda Kosong</h3>
            <p className="text-slate-500 dark:text-slate-400 text-sm">Manga yang Anda tandai akan muncul di sini.</p>
        </div>
      )}
    </div>
  );
};

export default BookmarkPage;