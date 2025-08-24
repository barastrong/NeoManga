import React from 'react';
import type  { History } from '../types/manga'; // Sesuaikan path jika perlu
import { differenceInSeconds } from 'date-fns';

const API_BASE_URL = 'http://127.0.0.1:8000';
interface HistoryCardProps {
  history: History;
  onDelete: (historyId: number) => void;
}

const formatRelativeTimeShort = (date: Date): string => {
  const seconds = differenceInSeconds(new Date(), date);
  const MINUTE = 60;
  const HOUR = MINUTE * 60;
  const DAY = HOUR * 24;
  const WEEK = DAY * 7;
  const MONTH = DAY * 30.44; 
  const YEAR = DAY * 365.25;

  if (seconds < MINUTE) return 'now';
  if (seconds < HOUR) return `${Math.floor(seconds / MINUTE)}m ago`;
  if (seconds < DAY) return `${Math.floor(seconds / HOUR)}h ago`;
  if (seconds < WEEK) return `${Math.floor(seconds / DAY)}d ago`;
  if (seconds < MONTH) return `${Math.floor(seconds / WEEK)}w ago`;
  if (seconds < YEAR) return `${Math.floor(seconds / MONTH)}mo ago`;
  
  return `${Math.floor(seconds / YEAR)}y ago`;
};

const getTypeFlag = (type: string) => {
  switch (type) {
    case 'manga':
      return { src: 'https://flagcdn.com/w40/jp.png', title: 'Manga (Japan)' };
    case 'manhwa':
      return { src: 'https://flagcdn.com/w40/kr.png', title: 'Manhwa (Korea)' };
    case 'manhua':
      return { src: 'https://flagcdn.com/w40/cn.png', title: 'Manhua (China)' };
    default:
      return null;
  }
};

const HistoryCard: React.FC<HistoryCardProps> = ({ history, onDelete }) => {
  if (!history.manga) return null; // Jangan render jika data manga tidak ada

  const flag = getTypeFlag(history.manga.type || '');
  const roundedRating = Math.round((history.manga.ratings_avg_rating || 0) * 2) / 2;

  const handleDelete = (e: React.MouseEvent) => {
    e.preventDefault();
    e.stopPropagation();
    if (window.confirm('Yakin ingin menghapus item ini dari riwayat?')) {
        onDelete(history.id);
    }
  }

  return (
    <div>
      <div className="relative">
        <button
          onClick={handleDelete}
          title="Hapus dari riwayat"
          className="absolute top-2 left-2 z-20 bg-red-600/80 hover:bg-red-600 text-white rounded-full p-1.5 shadow-lg transition-colors duration-300"
        >
          <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fillRule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm4 0a1 1 0 012 0v6a1 1 0 11-2 0V8z" clipRule="evenodd"></path></svg>
        </button>
        
        <a href={`/manga/${history.manga.slug}`} className="group">
          <div className="relative aspect-[3/4] rounded-md overflow-hidden shadow-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
            {history.manga.status === 'completed' && (
              <div className="absolute top-6 left-[-34px] transform -rotate-45 bg-red-600 text-white font-bold text-xs uppercase px-8 py-1 shadow-md z-10">
                Completed
              </div>
            )}
            
            <img 
              src={`${API_BASE_URL}/storage/${history.manga.cover_image}`}
              alt={history.manga.title}
              className="w-full h-full object-cover transition-transform duration-300 ease-in-out group-hover:scale-105"
            />

            {flag && (
                <div className="absolute top-2 right-2">
                    <img src={flag.src} alt={flag.title} className="w-10 h-6 rounded-sm object-cover shadow-md" title={flag.title} />
                </div>
            )}
          </div>
        </a>
      </div>

      <div className="mt-3">
        <a href={`/manga/${history.manga.slug}`}>
          <h3 className="font-bold text-base leading-tight truncate text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors" title={history.manga.title}>
            {history.manga.title}
          </h3>
        </a>
        
        {history.chapter ? (
          <a href={`/chapter/${history.chapter.slug}`} className="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
            <div className="flex justify-between items-center text-sm mt-2 border border-gray-300 dark:border-gray-600 rounded-md px-2 py-1">
              <span>Chapter {history.chapter.number}</span>
              <span>
                {formatRelativeTimeShort(new Date(history.created_at))}
              </span>
            </div>
          </a>
        ) : (
          <p className="mt-2 text-sm italic text-gray-500 dark:text-gray-400">Info chapter tidak tersedia</p>
        )}

        <div className="flex items-center mt-2">
          {[...Array(5)].map((_, i) => (
            <svg key={i} className={`w-4 h-4 ${i < roundedRating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600'}`} fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
          ))}
        </div>
      </div>
    </div>
  );
};

export default HistoryCard;