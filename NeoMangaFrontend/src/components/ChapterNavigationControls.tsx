import React, { useState, useEffect, useRef } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import type { ChapterDetail, ChapterNavigation } from '../types/manga';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faChevronLeft, faChevronRight, faChevronDown } from '@fortawesome/free-solid-svg-icons';

interface ChapterNavigationProps {
  currentChapter: ChapterDetail;
  prevChapter: ChapterNavigation | null;
  nextChapter: ChapterNavigation | null;
  allChapters: ChapterNavigation[];
  mangaSlug: string;
  isBottom?: boolean;
}

const ChapterNavigationControls: React.FC<ChapterNavigationProps> = ({ currentChapter, prevChapter, nextChapter, allChapters, mangaSlug, isBottom = false }) => {
  const [isOpen, setIsOpen] = useState(false);
  const dropdownRef = useRef<HTMLDivElement>(null);
  const navigate = useNavigate();

  useEffect(() => {
    const handleClickOutside = (event: MouseEvent) => {
      if (dropdownRef.current && !dropdownRef.current.contains(event.target as Node)) {
        setIsOpen(false);
      }
    };
    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);

  const handleChapterChange = (slug: string) => {
    setIsOpen(false);
    navigate(`/chapter/${slug}`);
  };

  const NavButton: React.FC<{ chapter: ChapterNavigation | null; direction: 'prev' | 'next' }> = ({ chapter, direction }) => {
    const isDisabled = !chapter;
    const isPrev = direction === 'prev';
    const linkTo = chapter ? `/chapter/${chapter.slug}` : `/manga/${mangaSlug}`;
    const title = chapter ? `${isPrev ? 'Previous' : 'Next'} Chapter` : 'Back to Manga';

    return (
      <Link
        to={linkTo}
        title={title}
        className={`flex items-center gap-2 px-3 py-2 rounded-md transition-colors ${isDisabled ? 'opacity-50 cursor-not-allowed bg-slate-200 dark:bg-slate-700' : 'bg-white dark:bg-slate-700 hover:bg-slate-100 dark:hover:bg-slate-600'}`}
      >
        {isPrev && <FontAwesomeIcon icon={faChevronLeft} />}
        <span className="hidden md:inline font-semibold">{isPrev ? 'Prev' : 'Next'}</span>
        {!isPrev && <FontAwesomeIcon icon={faChevronRight} />}
      </Link>
    );
  };

  return (
    <div className="bg-slate-50 dark:bg-slate-800 border-y border-slate-200 dark:border-slate-700 p-2 rounded-lg mb-4">
      <div className="flex items-center justify-between gap-4">
        <div className="relative w-full md:w-64" ref={dropdownRef}>
          <button
            onClick={() => setIsOpen(!isOpen)}
            className="w-full flex items-center justify-between px-4 py-2 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-md hover:bg-slate-100 dark:hover:bg-slate-600 transition-colors"
          >
            <span className="font-semibold truncate">Chapter {currentChapter.number}</span>
            <FontAwesomeIcon icon={faChevronDown} className={`text-xs transition-transform transform ml-2 ${isOpen ? 'rotate-180' : ''}`} />
          </button>
          {isOpen && (
            <div className={`absolute left-0 w-full bg-white dark:bg-slate-800 rounded-md shadow-lg max-h-80 overflow-y-auto z-50 border border-slate-200 dark:border-slate-700 ${isBottom ? 'bottom-full mb-2' : 'top-full mt-2'}`}>
              <ul className="text-sm p-1">
                {allChapters.slice().sort((a, b) => Number(a.number) - Number(b.number)).map(ch => (
                  <li key={ch.id}>
                    <button
                      onClick={() => handleChapterChange(ch.slug)}
                      className={`block w-full text-left px-3 py-2 rounded-md hover:bg-slate-100 dark:hover:bg-slate-700 ${ch.id === currentChapter.id ? 'bg-indigo-500 text-white font-bold' : ''}`}
                    >
                      Chapter {ch.number}
                    </button>
                  </li>
                ))}
              </ul>
            </div>
          )}
        </div>
        <div className="flex items-center gap-2 flex-shrink-0">
          <NavButton chapter={prevChapter} direction="prev" />
          <NavButton chapter={nextChapter} direction="next" />
        </div>
      </div>
    </div>
  );
};

export default ChapterNavigationControls;