// src/pages/HistoryPage.tsx

import React, { useState, useEffect, useCallback } from 'react';
import apiRoutes from '../routes/route';
import type { History, PaginatedResponse } from '../types/manga'; // Sesuaikan path
import HistoryCard from '../components/HistoryCard'; // Sesuaikan path

const HistoryPage: React.FC = () => {
  const [historyData, setHistoryData] = useState<PaginatedResponse<History> | null>(null);
  const [loading, setLoading] = useState<boolean>(true);
  const [error, setError] = useState<string | null>(null);
  const [notification, setNotification] = useState<string | null>(null);

  const fetchHistories = useCallback(async (url: string = '/history') => {
    setLoading(true);
    setError(null);
    try {
      // Pastikan token sudah di-set di instance axios Anda
      const response = await apiRoutes.get<PaginatedResponse<History>>(url);
      setHistoryData(response.data);
    } catch (err: any) {
      if (err.response && err.response.status === 401) {
        setError("Silakan login untuk melihat riwayat baca Anda.");
      } else {
        setError("Gagal memuat riwayat. Silakan coba lagi nanti.");
      }
      console.error(err);
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    fetchHistories();
  }, [fetchHistories]);

  const handleClearAll = async () => {
    if (window.confirm('Anda yakin ingin membersihkan semua riwayat? Aksi ini tidak dapat dibatalkan.')) {
        try {
            const response = await apiRoutes.delete('/history/clear');
            setNotification(response.data.message);
            setHistoryData(prev => prev ? { ...prev, data: [], total: 0 } : null);
        } catch (err) {
            setError('Gagal membersihkan riwayat.');
        }
    }
  };

  const handleDeleteItem = async (historyId: number) => {
    try {
        const response = await apiRoutes.delete(`/history/${historyId}`);
        setNotification(response.data.message);
        setHistoryData(prev => {
            if (!prev) return null;
            return {
                ...prev,
                data: prev.data.filter(item => item.id !== historyId),
                total: prev.total - 1,
            };
        });
    } catch (err) {
        setError('Gagal menghapus item riwayat.');
    }
  };


  if (loading) {
    return <div className="container mx-auto px-4 py-8 text-center">Memuat riwayat...</div>;
  }

  if (error) {
     return (
        <div className="container mx-auto px-4 py-8 md:px-6 md:py-10">
            <div className="text-center py-20 border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-lg">
                <svg className="w-16 h-16 mx-auto mb-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                <h3 className="text-xl font-medium mb-2 text-gray-900 dark:text-gray-100">Akses Riwayat Baca Anda</h3>
                <p className="text-gray-500 dark:text-gray-400 mb-6">{error}</p>
                <a href="/login" className="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-md text-sm transition-colors">
                    Login
                </a>
            </div>
        </div>
    );
  }

  const histories = historyData?.data || [];

  return (
    <div className="container mx-auto px-4 py-8 md:px-6 md:py-10">
      <div className="flex justify-between items-center mb-6">
        <h1 className="text-2xl font-bold text-gray-900 dark:text-white">History</h1>
        {histories.length > 0 && (
          <button 
            onClick={handleClearAll}
            className="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-md text-sm transition-colors">
            Clear All history
          </button>
        )}
      </div>

      {notification && (
        <div className="bg-green-100 dark:bg-green-500/20 border border-green-400 dark:border-green-500 text-green-700 dark:text-green-300 px-4 py-3 rounded relative mb-6" role="alert">
            <span>{notification}</span>
        </div>
      )}

      {histories.length > 0 ? (
        <>
          <div className="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-8 gap-x-4 gap-y-8">
            {histories.map(history => (
              <HistoryCard key={history.id} history={history} onDelete={handleDeleteItem} />
            ))}
          </div>

          {/* Pagination Controls */}
          <div className="mt-10 flex justify-center items-center gap-4">
              <button
                onClick={() => historyData?.prev_page_url && fetchHistories(historyData.prev_page_url)}
                disabled={!historyData?.prev_page_url || loading}
                className="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded disabled:opacity-50"
              >
                Previous
              </button>
              <span>
                Page {historyData?.current_page} of {historyData?.last_page}
              </span>
              <button
                onClick={() => historyData?.next_page_url && fetchHistories(historyData.next_page_url)}
                disabled={!historyData?.next_page_url || loading}
                className="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded disabled:opacity-50"
              >
                Next
              </button>
          </div>
        </>
      ) : (
        <div className="text-center py-20 border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-lg">
          <svg className="w-16 h-16 mx-auto mb-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
          <h3 className="text-xl font-medium mb-2 text-gray-900 dark:text-gray-100">Riwayat Baca Anda Kosong</h3>
          <p className="text-gray-500 dark:text-gray-400 text-sm">Manga yang Anda baca akan muncul di sini.</p>
        </div>
      )}
    </div>
  );
};

export default HistoryPage;