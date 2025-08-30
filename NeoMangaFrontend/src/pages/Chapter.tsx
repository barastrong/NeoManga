import React, { useState, useEffect } from 'react';
import { useParams, Link, useNavigate } from 'react-router-dom';
import apiRoutes from '../routes/route';
import type { ChapterDetail, ChapterNavigation } from '../types/manga';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faSpinner, faHome } from '@fortawesome/free-solid-svg-icons';
import ChapterNavigationControls from '../components/ChapterNavigationControls';
import ChapterReader from '../components/ChapterReader';
import CommentSection from '../components/CommentSection';

const ChapterPage: React.FC = () => {
  const { slug } = useParams<{ slug: string }>();
  const navigate = useNavigate();
  const [data, setData] = useState<{
    chapter: ChapterDetail;
    prev_chapter: ChapterNavigation | null;
    next_chapter: ChapterNavigation | null;
    all_chapters: ChapterNavigation[];
  } | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchChapter = async () => {
      if (!slug) return;
      setLoading(true);
      try {
        const response = await apiRoutes.get(`/chapter/${slug}`);
        setData(response.data);
      } catch (error) {
        console.error("Failed to fetch chapter:", error);
        navigate('/not-found');
      } finally {
        setLoading(false);
      }
    };
    fetchChapter();
    window.scrollTo(0, 0);
  }, [slug, navigate]);

  if (loading || !data) {
    return (
      <div className="flex justify-center items-center min-h-screen">
        <FontAwesomeIcon icon={faSpinner} spin size="3x" className="text-indigo-500" />
      </div>
    );
  }

  const { chapter, prev_chapter, next_chapter, all_chapters } = data;
  const storageUrl = import.meta.env.VITE_STORAGE_URL || 'http://127.0.0.1:8000/storage/';
  const imageUrls = chapter.chapter_images.map(imagePath => `${storageUrl}${imagePath}`);

  return (
    <div className="min-h-screen">
      <div className="container mx-auto px-2 sm:px-4 py-8">
        <div className="max-w-4xl mx-auto">
          <div className="mb-6 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg p-4 shadow-lg">
            <nav className="flex items-center space-x-2 text-sm text-slate-500 dark:text-slate-400 mb-4 overflow-x-auto whitespace-nowrap">
              <Link to="/" className="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                <FontAwesomeIcon icon={faHome} /> Home
              </Link>
              <span>/</span>
              <Link to={`/manga/${chapter.manga.slug}`} className="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors truncate">
                {chapter.manga.title}
              </Link>
              <span>/</span>
              <span className="font-semibold text-slate-800 dark:text-slate-200">
                Chapter {chapter.number}
              </span>
            </nav>
            <div className="border-t border-slate-200 dark:border-slate-700 pt-4 text-center">
              <h1 className="text-3xl font-bold text-slate-900 dark:text-white">{chapter.manga.title} - Chapter {chapter.number}</h1>
            </div>
          </div>

          <ChapterNavigationControls
            currentChapter={chapter}
            prevChapter={prev_chapter}
            nextChapter={next_chapter}
            allChapters={all_chapters}
            mangaSlug={chapter.manga.slug}
          />

          <ChapterReader
            imageUrls={imageUrls}
            mangaTitle={chapter.manga.title}
            chapterNumber={chapter.number}
          />
          
          <div className="border-t border-slate-200 dark:border-slate-700 pt-6 mb-12">
            <ChapterNavigationControls
              isBottom
              currentChapter={chapter}
              prevChapter={prev_chapter}
              nextChapter={next_chapter}
              allChapters={all_chapters}
              mangaSlug={chapter.manga.slug}
            />
          </div>
          
          <div id="comment-section" className="mt-8">
            <CommentSection 
              mangaId={chapter.manga.id} 
              chapterId={chapter.id} 
            />
          </div>
          
        </div>
      </div>
    </div>
  );
};

export default ChapterPage;