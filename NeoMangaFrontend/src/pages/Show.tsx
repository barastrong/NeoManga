import React, { useState, useEffect, useMemo } from 'react';
import { useParams, Link, useNavigate } from 'react-router-dom';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faBookmark, faStar, faStarHalfAlt, faSearch, faSortDown, faSortUp, faTimes, faSpinner } from '@fortawesome/free-solid-svg-icons';
import { faStar as farStar } from '@fortawesome/free-solid-svg-icons';
import { format } from 'date-fns';
import apiRoutes from '../routes/route';
import type { MangaDetail, Chapter, HistoryItem } from '../types/manga';
import { useAuth } from '../context/AuthContext';
import { toggleBookmark } from '../services/BookmarkService';
import CommentSection from '../components/CommentSection';
import LoginPromptModal from '../components/LoginPromptModal';

const DetailItem: React.FC<{ label: string; value?: string | null; capitalize?: boolean }> = ({ label, value, capitalize }) => {
  if (!value || value.trim() === '-' || value.trim() === '') return null;
  return (
    <div>
      <dt className="text-sm font-medium text-slate-500 dark:text-slate-400">{label}</dt>
      <dd className={`mt-1 text-sm text-slate-900 dark:text-white ${capitalize ? 'capitalize' : ''}`}>{value}</dd>
    </div>
  );
};

const RatingStars: React.FC<{ rating?: number }> = ({ rating = 0 }) => {
  const roundedRating = Math.round(rating * 2) / 2;
  return (
    <div className="flex items-center justify-center space-x-1 text-yellow-400">
      {Array.from({ length: 5 }).map((_, i) => {
        if (roundedRating >= i + 1) return <FontAwesomeIcon key={i} icon={faStar} />;
        if (roundedRating >= i + 0.5) return <FontAwesomeIcon key={i} icon={faStarHalfAlt} />;
        return <FontAwesomeIcon key={i} icon={farStar} />;
      })}
    </div>
  );
};

const ChapterList: React.FC<{ chapters: Chapter[], readChapterIds: number[] }> = ({ chapters, readChapterIds }) => {
    const [searchTerm, setSearchTerm] = useState('');
    const [sortOrder, setSortOrder] = useState<'desc' | 'asc'>('desc');
    const filteredAndSortedChapters = useMemo(() => chapters.filter(ch => ch.number.toLowerCase().includes(searchTerm.toLowerCase())).sort((a, b) => sortOrder === 'desc' ? parseFloat(b.number) - parseFloat(a.number) : parseFloat(a.number) - parseFloat(b.number)), [chapters, searchTerm, sortOrder]);
    const isNew = (date: string) => new Date(date) > new Date(Date.now() - 86400000);
    
    return (
      <div className="bg-white dark:bg-slate-800 rounded-lg p-6 mb-8 shadow-lg border border-slate-200 dark:border-slate-700">
        <div className="flex flex-wrap items-center justify-between gap-4 pb-4 border-b border-slate-200 dark:border-slate-700 mb-4">
          <h2 className="text-2xl font-bold text-slate-800 dark:text-white">Daftar Chapter</h2>
          <div className="flex items-center gap-2 w-full sm:w-auto">
            <div className="relative flex-grow">
              <span className="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"><FontAwesomeIcon icon={faSearch} className="text-slate-400" /></span>
              <input type="text" value={searchTerm} onChange={e => setSearchTerm(e.target.value)} placeholder="Cari Chapter" className="w-full pl-10 pr-10 py-2 bg-slate-100 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg" />
              {searchTerm && <button onClick={() => setSearchTerm('')} className="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600"><FontAwesomeIcon icon={faTimes} /></button>}
            </div>
            <button onClick={() => setSortOrder(o => o === 'desc' ? 'asc' : 'desc')} className="flex-shrink-0 bg-slate-200 dark:bg-slate-600 font-medium py-2 px-4 rounded-lg flex items-center gap-2">
              <FontAwesomeIcon icon={sortOrder === 'desc' ? faSortDown : faSortUp} />
              <span className="hidden sm:inline">{sortOrder === 'desc' ? 'Terbaru' : 'Terlama'}</span>
            </button>
          </div>
        </div>
        {filteredAndSortedChapters.length > 0 ? (
          <div className="max-h-[450px] overflow-y-auto pr-2">
            <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-2">
              {filteredAndSortedChapters.map(chapter => (
                <div key={chapter.id} className="bg-slate-50 hover:bg-slate-100 dark:bg-slate-700 dark:hover:bg-slate-600 rounded-lg p-2.5 border border-slate-300 dark:border-slate-600 hover:border-indigo-500">
                  <Link to={`/chapter/${chapter.slug}`} className="block">
                    <div className="flex items-center justify-between mb-1">
                      <div className={`text-sm font-medium ${readChapterIds.includes(chapter.id) ? 'text-indigo-600 dark:text-indigo-500' : 'text-slate-800 dark:text-gray-200'}`}>
                        Ch. {chapter.number}{isNew(chapter.created_at) && <span className="text-red-500 font-bold ml-1.5">New</span>}
                      </div>
                      {chapter.status === 'fixed' && <span className="text-xs bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 px-1.5 py-0.5 rounded-md">Fixed</span>}
                    </div>
                    <div className="text-xs text-slate-500 dark:text-slate-400">{format(new Date(chapter.created_at), 'd MMM yyyy')}</div>
                  </Link>
                </div>
              ))}
            </div>
          </div>
        ) : (
          <div className="text-center py-12 text-slate-500 dark:text-slate-400">Chapter tidak ditemukan.</div>
        )}
      </div>
    );
};

const MangaDetailPage: React.FC = () => {
  const { slug } = useParams<{ slug: string }>();
  const navigate = useNavigate();
  const { isAuthenticated, isLoading: isAuthLoading } = useAuth();
  const [manga, setManga] = useState<MangaDetail | null>(null);
  const [chapters, setChapters] = useState<Chapter[]>([]);
  const [isBookmarked, setIsBookmarked] = useState(false);
  const [followersCount, setFollowersCount] = useState(0);
  const [readChapters, setReadChapters] = useState<number[]>([]);
  const [userHistories, setUserHistories] = useState<HistoryItem[]>([]);
  const [loading, setLoading] = useState(true);
  const [bookmarkLoading, setBookmarkLoading] = useState(false);
  const [isLoginModalOpen, setIsLoginModalOpen] = useState(false);

  useEffect(() => {
    const fetchData = async () => {
      if (!slug) return;
      setLoading(true);
      try {
        const response = await apiRoutes.get(`/manga/${slug}`);
        const data = response.data;
        
        setManga(data.manga);
        setChapters(data.chapters);
        setIsBookmarked(data.isBookmarked);
        setReadChapters(data.readChapters);
        setUserHistories(data.userHistories);
        setFollowersCount(data.manga.followers_count);
      } catch (error) {
        console.error("Gagal memuat detail manga", error);
        navigate('/not-found', { replace: true });
      } finally {
        setLoading(false);
      }
    };
    
    if (!isAuthLoading) {
      fetchData();
    }
  }, [slug, isAuthLoading, navigate]);

  const handleBookmarkClick = async () => {
    if (!isAuthenticated) {
      setIsLoginModalOpen(true);
      return;
    }
    if (!manga || bookmarkLoading) return;
    
    setBookmarkLoading(true);
    try {
      const response = await toggleBookmark(manga.slug);
      setIsBookmarked(response.is_bookmarked);
      setFollowersCount(response.followers_count);
    } catch (error) {
      console.error("Gagal mengubah bookmark", error);
    } finally {
      setBookmarkLoading(false);
    }
  };

  if (loading || isAuthLoading) {
    return <div className="flex justify-center items-center h-screen"><FontAwesomeIcon icon={faSpinner} spin size="3x" /></div>;
  }
  
  if (!manga) {
    return <div className="text-center py-20">Manga tidak ditemukan.</div>;
  }

  const storageUrl = import.meta.env.VITE_STORAGE_URL || 'http://127.0.0.1:8000/storage/';
  const coverImage = manga.cover_image ? `${storageUrl}${manga.cover_image}` : '/images/no-image.png';

  return (
    <>
      <div className="container mx-auto px-4 py-8">
        <div className="bg-white dark:bg-slate-800 rounded-lg p-6 mb-8 shadow-lg border border-slate-200 dark:border-slate-700">
          <div className="text-center md:text-left mb-6">
            <h1 className="text-3xl lg:text-4xl font-bold text-slate-800 dark:text-white">{manga.title}</h1>
            {manga.alternative_title && <h2 className="text-lg text-slate-600 dark:text-slate-400 mt-1 italic">{manga.alternative_title}</h2>}
          </div>
          <div className="flex flex-col md:flex-row gap-8">
            <div className="flex-shrink-0 w-full md:w-48">
              <img src={coverImage} alt={manga.title} className="w-48 h-64 object-cover rounded-lg shadow-lg mx-auto" />
              <button onClick={handleBookmarkClick} disabled={bookmarkLoading} className={`w-full mt-4 font-bold py-2 px-4 rounded-lg transition-colors duration-200 disabled:opacity-50 ${isBookmarked ? 'bg-green-600 hover:bg-green-700' : 'bg-indigo-600 hover:bg-indigo-700'} text-white`}>
                <FontAwesomeIcon icon={faBookmark} className="mr-2" />
                {bookmarkLoading ? 'Loading...' : (isBookmarked ? 'Hapus Bookmark' : 'Bookmark')}
              </button>
              <div className="mt-4 text-center">
                <div className="text-sm mb-2 text-slate-700 dark:text-slate-300">
                  Diikuti oleh <span className="font-semibold text-indigo-600">{followersCount}</span> orang
                </div>
                <RatingStars rating={manga.ratings_avg_rating} />
              </div>
            </div>
            <div className="flex-1">
              <p className="mb-6 leading-relaxed text-slate-700 dark:text-slate-300">{manga.description}</p>
              <div className="border-t border-slate-200 dark:border-slate-700 pt-6">
                <dl className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4">
                  <DetailItem label="Status" value={manga.status} capitalize />
                  <DetailItem label="Tipe" value={manga.type} capitalize />
                  <DetailItem label="Author" value={manga.author} />
                  <DetailItem label="Artist" value={manga.artist} />
                  <DetailItem label="Diposting oleh" value={manga.user.name} />
                  <DetailItem label="Tanggal Rilis" value={format(new Date(manga.created_at), 'd MMM yyyy')} />
                  <DetailItem label="Update Terakhir" value={format(new Date(manga.updated_at), 'd MMM yyyy')} />
                  <div className="sm:col-span-2 lg:col-span-3">
                    <dt className="text-sm font-medium text-slate-500 dark:text-slate-400">Genre</dt>
                    <dd className="mt-2 flex flex-wrap gap-2">
                      {manga.genres.map(genre => <Link key={genre.id} to={`/manga-list?genre[]=${genre.id}`} className="bg-indigo-600 hover:bg-indigo-700 px-3 py-1 rounded-full text-xs text-white">{genre.name}</Link>)}
                    </dd>
                  </div>
                </dl>
              </div>
            </div>
          </div>
        </div>

        {userHistories && userHistories.length > 0 && (
          <div className="bg-white dark:bg-slate-800 rounded-lg p-6 mb-8 shadow-lg border border-slate-200 dark:border-slate-700">
            <h2 className="text-xl font-bold text-slate-800 dark:text-white mb-4">Terakhir Dibaca</h2>
            <div className="max-h-48 overflow-y-auto pr-2">
              <div className="space-y-3">
                {userHistories.map(history => (
                  <div key={history.id} className="flex justify-between items-center border border-slate-300 dark:border-slate-600 p-3 rounded-lg">
                    <Link to={`/chapter/${history.chapter.slug}`} className="font-medium text-slate-700 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-500">
                      Chapter {history.chapter.number}
                    </Link>
                    <span className="text-sm text-slate-500 dark:text-slate-400">
                      {format(new Date(history.updated_at), 'd MMM yyyy, HH:mm')}
                    </span>
                  </div>
                ))}
              </div>
            </div>
          </div>
        )}

        <ChapterList chapters={chapters} readChapterIds={readChapters} />
        
        {manga.comments && <CommentSection initialComments={manga.comments} mangaId={manga.id} />}
      </div>
      
      <LoginPromptModal isOpen={isLoginModalOpen} onClose={() => setIsLoginModalOpen(false)} />
    </>
  );
};

export default MangaDetailPage;