import React, { useState, useEffect, useCallback } from 'react';
import { getCommentsForChapter, deleteComment } from '../services/CommentService';
import type { Comment } from '../types/manga';
import CommentForm from './CommentForm';
import CommentItem from './CommentItem';
import { useAuth } from '../context/AuthContext';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faSpinner, faComments } from '@fortawesome/free-solid-svg-icons';

interface CommentSectionProps {
  mangaId: number;
  chapterId: number;
}

const CommentSection: React.FC<CommentSectionProps> = ({ mangaId, chapterId }) => {
  const { isAuthenticated } = useAuth();
  const [comments, setComments] = useState<Comment[]>([]);
  const [loading, setLoading] = useState(true);

  const fetchComments = useCallback(async () => {
    try {
      setLoading(true);
      const fetchedComments = await getCommentsForChapter(chapterId);
      setComments(fetchedComments);
    } catch (error) {
      console.error("Gagal memuat komentar:", error);
    } finally {
      setLoading(false);
    }
  }, [chapterId]);

  useEffect(() => {
    fetchComments();
  }, [fetchComments]);

  const handleCommentPosted = (newComment: Comment) => {
    if (newComment.parent_id) {
      setComments(prevComments => 
        prevComments.map(comment => 
          comment.id === newComment.parent_id
            ? { ...comment, replies: [newComment, ...comment.replies] }
            : comment
        )
      );
    } else {
      setComments(prevComments => [newComment, ...prevComments]);
    }
  };
  
  const handleDelete = async (commentId: number) => {
    if (!window.confirm("Apakah Anda yakin ingin menghapus komentar ini?")) return;
    try {
      await deleteComment(commentId);
      setComments(prev => 
        prev.map(c => ({
          ...c,
          replies: c.replies.filter(r => r.id !== commentId)
        })).filter(c => c.id !== commentId)
      );
    } catch (error) {
      console.error("Gagal menghapus komentar:", error);
    }
  };

  return (
    <div className="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg p-4 sm:p-6 shadow-lg">
      <h2 className="text-2xl font-bold text-slate-900 dark:text-white mb-6 flex items-center">
        <FontAwesomeIcon icon={faComments} className="mr-3 text-red-500" />
        Komentar
      </h2>
      
      {isAuthenticated ? (
        <CommentForm
          mangaId={mangaId}
          chapterId={chapterId}
          onCommentPosted={handleCommentPosted}
        />
      ) : (
        <div className="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg text-center">
          <p className="text-blue-800 dark:text-blue-200">
            <a href="/login" className="font-semibold underline hover:text-blue-900 dark:hover:text-blue-100">Login</a> untuk berkomentar
          </p>
        </div>
      )}
      
      <div className="space-y-6">
        {loading ? (
          <div className="text-center py-8">
            <FontAwesomeIcon icon={faSpinner} spin size="2x" className="text-slate-400" />
            <p className="mt-2 text-slate-500 dark:text-slate-400">Memuat komentar...</p>
          </div>
        ) : comments.length > 0 ? (
          comments.map(comment => (
            <CommentItem
              key={comment.id}
              comment={comment}
              mangaId={mangaId}
              chapterId={chapterId}
              onReplyPosted={handleCommentPosted}
              onDelete={handleDelete}
            />
          ))
        ) : (
          <div className="text-center py-8 text-slate-500 dark:text-slate-400">
            <p>Jadilah yang pertama berkomentar!</p>
          </div>
        )}
      </div>
    </div>
  );
};

export default CommentSection;