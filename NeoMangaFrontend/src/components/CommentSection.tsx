import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import CommentForm from './CommentForm';
import CommentItem from './CommentItem';
import type { Comment } from '../types/manga';
import { deleteComment } from '../services/CommentService';
import ConfirmationModal from './ConfirmationModal';

const CommentSection: React.FC<{ initialComments: Comment[], mangaId: number }> = ({ initialComments, mangaId }) => {
  const { isAuthenticated } = useAuth();
  const [comments, setComments] = useState<Comment[]>(initialComments);
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [commentToDelete, setCommentToDelete] = useState<number | null>(null);

  const handleCommentPosted = (newItem: Comment) => {
    setComments(currentComments => {
      if (newItem.parent_id) {
        const updateReplies = (comments: Comment[]): Comment[] => comments.map(c => c.id === newItem.parent_id ? { ...c, replies: [...c.replies, newItem] } : { ...c, replies: updateReplies(c.replies) });
        return updateReplies(currentComments);
      }
      return [newItem, ...currentComments];
    });
  };
  
  const promptDeleteComment = (commentId: number) => {
    setCommentToDelete(commentId);
    setIsModalOpen(true);
  };

  const confirmDeleteComment = async () => {
    if (commentToDelete === null) return;
    try {
      await deleteComment(commentToDelete);
      const filterOutComment = (comments: Comment[]): Comment[] => comments.filter(c => c.id !== commentToDelete).map(c => ({ ...c, replies: filterOutComment(c.replies) }));
      setComments(currentComments => filterOutComment(currentComments));
    } catch (error) {
      console.error("Gagal menghapus komentar", error);
    } finally {
      setIsModalOpen(false);
      setCommentToDelete(null);
    }
  };
  
  return (
    <div className="bg-white dark:bg-slate-800 rounded-lg p-6 shadow-lg border border-slate-200 dark:border-slate-700">
      <h2 className="text-2xl font-bold mb-6 text-slate-800 dark:text-white">Komentar ({comments.reduce((acc, c) => acc + 1 + c.replies.length, 0)})</h2>
      {isAuthenticated ? (
        <CommentForm mangaId={mangaId} onCommentPosted={handleCommentPosted} />
      ) : (
        <div className="border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-lg p-8 text-center mb-8">
          <h3 className="text-lg font-semibold text-slate-800 dark:text-white">Gabung Diskusi!</h3>
          <p className="mt-2 text-slate-600 dark:text-slate-400">Anda harus login untuk mengirim komentar.</p>
          <Link to="/login" state={{ from: window.location }}>
            <button className="mt-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
              Login untuk Berkomentar
            </button>
          </Link>
        </div>
      )}
      <div className="space-y-6">
        {comments.length > 0 ? (
          comments.map(comment => (
            <CommentItem 
              key={comment.id} 
              comment={comment} 
              mangaId={mangaId} 
              onReplyPosted={handleCommentPosted} 
              onDelete={promptDeleteComment}
            />
          ))
        ) : (
          <div className="text-center py-8 text-slate-500 dark:text-slate-400">
            Belum ada komentar. Jadilah yang pertama berkomentar!
          </div>
        )}
      </div>

      <ConfirmationModal
        isOpen={isModalOpen}
        onClose={() => setIsModalOpen(false)}
        onConfirm={confirmDeleteComment}
        title="Konfirmasi Hapus Komentar"
        message="Apakah Anda yakin ingin menghapus komentar ini secara permanen? Tindakan ini tidak dapat diurungkan."
      />
    </div>
  );
};

export default CommentSection;