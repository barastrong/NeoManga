import React from 'react';
import { Link } from 'react-router-dom';
import type { Bookmark } from '../types/manga';
import { differenceInSeconds } from 'date-fns';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faTrash } from '@fortawesome/free-solid-svg-icons';

const formatRelativeTimeShort = (date: Date): string => {
  const seconds = differenceInSeconds(new Date(), date);
  const MINUTE = 60;
  const HOUR = MINUTE * 60;
  const DAY = HOUR * 24;
  const WEEK = DAY * 7;
  const MONTH = DAY * 30.44; 
  const YEAR = DAY * 365.25;

  if (seconds < MINUTE) return 'now';
  if (seconds < HOUR) return `${Math.floor(seconds / MINUTE)}m`;
  if (seconds < DAY) return `${Math.floor(seconds / HOUR)}h`;
  if (seconds < WEEK) return `${Math.floor(seconds / DAY)}d`;
  if (seconds < MONTH) return `${Math.floor(seconds / WEEK)}w`;
  if (seconds < YEAR) return `${Math.floor(seconds / MONTH)}mo`;
  
  return `${Math.floor(seconds / YEAR)}y`;
};

const getTypeFlag = (type?: string) => {
  switch (type) {
    case 'manga': return { src: 'https://flagcdn.com/w40/jp.png', title: 'Manga (Japan)' };
    case 'manhwa': return { src: 'https://flagcdn.com/w40/kr.png', title: 'Manhwa (Korea)' };
    case 'manhua': return { src: 'https://flagcdn.com/w40/cn.png', title: 'Manhua (China)' };
    default: return null;
  }
};

const StarIcon: React.FC<{ className?: string }> = ({ className }) => (
    <svg className={`w-4 h-4 ${className}`} fill="currentColor" viewBox="0 0 20 20">
      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
    </svg>
);

interface BookmarkCardProps {
  bookmark: Bookmark;
  onDelete: (bookmarkId: number) => void;
}

const BookmarkCard: React.FC<BookmarkCardProps> = ({ bookmark, onDelete }) => {
  if (!bookmark.manga) return null;

  const { manga } = bookmark;
  const flag = getTypeFlag(manga.type);
  const roundedRating = manga.ratings_avg_rating ? Math.round(manga.ratings_avg_rating) : 0;
  const storageUrl = import.meta.env.VITE_STORAGE_URL || 'http://127.0.0.1:8000/storage/';
  const coverImage = manga.cover_image ? `${storageUrl}${manga.cover_image}` : '/images/no-image.png';

  const handleDelete = (e: React.MouseEvent) => {
    e.preventDefault();
    e.stopPropagation();
    if (window.confirm('Yakin ingin menghapus bookmark ini?')) {
        onDelete(bookmark.id);
    }
  };

  return (
    <div>
      <div className="relative group">
        <button
          onClick={handleDelete} title="Hapus bookmark"
          className="absolute top-2 left-2 z-20 bg-red-600/80 hover:bg-red-600 text-white rounded-full w-7 h-7 flex items-center justify-center shadow-lg transition-all duration-300 opacity-0 group-hover:opacity-100">
          <FontAwesomeIcon icon={faTrash} className="h-3.5 w-3.5" />
        </button>
        <Link to={`/manga/${manga.slug}`}>
          <div className="relative aspect-[3/4] rounded-md overflow-hidden shadow-lg bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
            {manga.status === 'completed' && (
              <div className="absolute top-6 left-[-34px] transform -rotate-45 bg-red-600 text-white font-bold text-xs uppercase px-8 py-1 shadow-md z-10">
                Completed
              </div>
            )}
            <img src={coverImage} alt={manga.title}
              className="w-full h-full object-cover transition-transform duration-300 ease-in-out group-hover:scale-105" />
            {flag && (
              <div className="absolute top-2 right-2">
                <img src={flag.src} alt={flag.title} className="w-8 h-auto rounded-sm object-cover shadow-md" title={flag.title} />
              </div>
            )}
          </div>
        </Link>
      </div>
      <div className="mt-2.5">
        <Link to={`/manga/${manga.slug}`}>
          <h3 className="font-bold text-base leading-tight truncate text-slate-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors" title={manga.title}>
            {manga.title}
          </h3>
        </Link>
        <div className="flex items-center mt-1.5 space-x-1">
            {[...Array(5)].map((_, i) => (
                <StarIcon key={i} className={i < roundedRating ? 'text-yellow-400' : 'text-slate-300 dark:text-slate-600'} />
            ))}
        </div>
        {manga.latest_published_chapter ? (
          <Link to={`/chapter/${manga.latest_published_chapter.slug}`} className="block text-sm mt-2">
            <div className="flex justify-between items-center border border-slate-300 dark:border-slate-600 rounded-md px-2 py-1 text-slate-600 dark:text-slate-400 hover:border-indigo-500 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
              <span>Chapter {manga.latest_published_chapter.number}</span>
              <span className="text-xs">{formatRelativeTimeShort(new Date(manga.latest_published_chapter.created_at))}</span>
            </div>
          </Link>
        ) : (
          <p className="mt-2 text-sm italic text-slate-500 dark:text-slate-400 h-[34px] flex items-center">Belum ada chapter</p>
        )}
      </div>
    </div>
  );
};

export default BookmarkCard;