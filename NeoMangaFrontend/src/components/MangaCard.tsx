import React from 'react';
import { Link } from 'react-router-dom';
import { differenceInSeconds } from 'date-fns';
import type { MangaItem } from '../types/manga';
import RatingStars from './RatingStars';

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

const TypeFlag: React.FC<{ type: MangaItem['type'] }> = ({ type }) => {
  if (!type) return null;
  const flags = {
    manga: { src: 'https://flagcdn.com/w40/jp.png', title: 'Manga (Japan)' },
    manhwa: { src: 'https://flagcdn.com/w40/kr.png', title: 'Manhwa (Korea)' },
    manhua: { src: 'https://flagcdn.com/w40/cn.png', title: 'Manhua (China)' },
  };
  const flag = flags[type];
  return <img src={flag.src} alt={type} className="w-10 h-6 rounded-sm object-cover shadow-md" title={flag.title} />;
};

const MangaCard: React.FC<{ manga: MangaItem }> = ({ manga }) => {
  const storageUrl = import.meta.env.VITE_STORAGE_URL || 'http://127.0.0.1:8000/storage/';
  const chapter = manga.latest_published_chapter;

  const timeAgo = chapter?.created_at
    ? formatRelativeTimeShort(new Date(chapter.created_at))
    : '';

  return (
    <div>
      <div className="relative group">
        <Link to={`/manga/${manga.slug}`}>
          <div className="relative aspect-[3/4] rounded-md overflow-hidden shadow-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
            {manga.status === 'completed' && (
              <div className="absolute top-6 left-[-34px] transform -rotate-45 bg-red-600 text-white font-bold text-xs uppercase px-8 py-1 shadow-md z-10">
                Completed
              </div>
            )}
            {manga.cover_image ? (
              <img
                src={`${storageUrl}${manga.cover_image}`}
                alt={manga.title}
                className="w-full h-full object-cover transition-transform duration-300 ease-in-out group-hover:scale-105"
                loading="lazy"
              />
            ) : (
              <div className="w-full h-full flex items-center justify-center bg-gray-100 dark:bg-gray-700">
                <svg className="w-12 h-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
              </div>
            )}
            <div className="absolute top-2 right-2">
              <TypeFlag type={manga.type} />
            </div>
          </div>
        </Link>
      </div>
      <div className="mt-3">
        <Link to={`/manga/${manga.slug}`}>
          <h3 className="font-bold text-base text-gray-900 dark:text-white leading-tight truncate hover:text-blue-600 dark:hover:text-blue-400 transition-colors" title={manga.title}>
            {manga.title}
          </h3>
        </Link>
        {chapter ? (
          <Link to={`/chapter/${chapter.slug}`} className="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
            <div className="flex justify-between items-center text-sm mt-2 border border-gray-300 dark:border-gray-600 rounded-md px-2 py-1">
              <span>Chapter {chapter.number}</span>
              <span>{timeAgo}</span>
            </div>
          </Link>
        ) : (
          <p className="mt-2 text-sm text-gray-500 dark:text-gray-400 italic">Belum ada chapter</p>
        )}
        <div className="flex items-center mt-2">
          <RatingStars rating={manga.ratings_avg_rating || 0} />
        </div>
      </div>
    </div>
  );
};

export default MangaCard;