import React, { useState, type FormEvent } from 'react';
import { useAuth } from '../context/AuthContext';
import { postComment } from '../services/CommentService';
import type { Comment } from '../types/manga';

interface CommentFormProps {
  mangaId: number;
  chapterId: number;
  parentId?: number;
  onCommentPosted: (newComment: Comment) => void;
  onCancel?: () => void;
  initialContent?: string;
}

const CommentForm: React.FC<CommentFormProps> = ({
  mangaId,
  chapterId,
  parentId,
  onCommentPosted,
  onCancel,
  initialContent = '',
}) => {
  const { user } = useAuth();
  const [content, setContent] = useState(initialContent);
  const [loading, setLoading] = useState(false);

  const handleSubmit = async (e: FormEvent) => {
    e.preventDefault();
    if (!content.trim()) return;
    setLoading(true);
    try {
      const newComment = await postComment(mangaId, chapterId, content, parentId);
      onCommentPosted(newComment);
      setContent('');
      if (onCancel) onCancel();
    } catch (error) {
      console.error('Gagal mengirim komentar:', error);
    } finally {
      setLoading(false);
    }
  };

  if (!user) return null;

  return (
    <form onSubmit={handleSubmit} className="mb-8">
      <div className="flex items-start space-x-4">
        <img className="w-10 h-10 rounded-full object-cover" src={`https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=random&color=fff`} alt="Avatar" />
        <div className="flex-1">
          <textarea value={content} onChange={(e) => setContent(e.target.value)} rows={parentId ? 2 : 3}
            className="w-full p-3 bg-slate-100 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-red-500 focus:border-red-500 transition"
            placeholder={parentId ? "Tulis balasan..." : "Tambahkan komentar publik..."} required />
          <div className="flex justify-end items-center gap-3 mt-2">
            {onCancel && (<button type="button" onClick={onCancel} className="text-sm font-medium text-slate-600 dark:text-slate-400 hover:underline">Batal</button>)}
            <button type="submit" disabled={loading} className="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200 disabled:opacity-50">
              {loading ? 'Mengirim...' : (parentId ? 'Balas' : 'Kirim Komentar')}
            </button>
          </div>
        </div>
      </div>
    </form>
  );
};

export default CommentForm;