import React, { useState } from 'react';
import { formatDistanceToNow } from 'date-fns';
import { useAuth } from '../context/AuthContext';
import CommentForm from './CommentForm';
import type { Comment } from '../types/manga';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faHeart as fasHeart, faReply, faTrashAlt } from '@fortawesome/free-solid-svg-icons';
import { faHeart as farHeart } from '@fortawesome/free-solid-svg-icons';
import { toggleLike } from '../services/CommentService';

interface CommentItemProps {
  comment: Comment;
  mangaId: number;
  chapterId: number;
  onReplyPosted: (newReply: Comment) => void;
  onDelete: (commentId: number) => void;
}

const CommentItem: React.FC<CommentItemProps> = ({
  comment,
  mangaId,
  chapterId,
  onReplyPosted,
  onDelete,
}) => {
  const { user, isAuthenticated } = useAuth();
  const [isReplying, setIsReplying] = useState(false);
  const [likeCount, setLikeCount] = useState(comment.likes_count);
  const [isLiked, setIsLiked] = useState(comment.is_liked_by_user || false);

  const handleReplySuccess = (newReply: Comment) => {
    onReplyPosted(newReply);
    setIsReplying(false);
  };

  const handleLike = async () => {
    if (!isAuthenticated) return;
    const originalIsLiked = isLiked;
    const originalLikeCount = likeCount;
    setIsLiked(prev => !prev);
    setLikeCount(prev => originalIsLiked ? prev - 1 : prev + 1);
    try {
      const response = await toggleLike(comment.id);
      setIsLiked(response.liked);
      setLikeCount(response.likes_count);
    } catch (error) {
      console.error("Gagal like komentar:", error);
      setIsLiked(originalIsLiked);
      setLikeCount(originalLikeCount);
    }
  };

  return (
    <div id={`comment-${comment.id}`} className="flex items-start space-x-4">
      <img className="w-10 h-10 rounded-full object-cover" src={`https://ui-avatars.com/api/?name=${encodeURIComponent(comment.user.name)}&background=random&color=fff`} alt="Avatar" />
      <div className="flex-1">
        <div className="bg-slate-100 dark:bg-slate-700 rounded-lg p-4">
          <div className="flex items-center justify-between">
            <div className="flex items-center space-x-2">
              <span className={`font-semibold ${comment.user.role === 'admin' ? 'text-red-500 dark:text-red-400' : 'text-slate-800 dark:text-white'}`}>{comment.user.name}</span>
              {comment.user.role === 'admin' && (<span className="bg-red-500 text-white text-xs font-semibold px-2 py-0.5 rounded-full">Admin</span>)}
            </div>
            <span className="text-xs text-slate-500 dark:text-slate-400 flex-shrink-0 ml-4">{formatDistanceToNow(new Date(comment.created_at))} lalu</span>
          </div>
          <p
            className="mt-2 text-base text-slate-800 dark:text-gray-200 whitespace-pre-wrap"
            dangerouslySetInnerHTML={{ __html: comment.content }}
          />
        </div>
        {isAuthenticated && (
          <div className="flex items-center space-x-4 mt-2 pl-2 text-sm">
            <button onClick={handleLike} className={`font-medium transition ${isLiked ? 'text-red-500' : 'text-slate-500 dark:text-slate-400 hover:text-red-500'}`}>
              <FontAwesomeIcon icon={isLiked ? fasHeart : farHeart} className="mr-1" />
              <span>{likeCount}</span>
            </button>
            <button onClick={() => setIsReplying(!isReplying)} className="font-medium text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition">
              <FontAwesomeIcon icon={faReply} className="mr-1" /> Balas
            </button>
            {user && user.role === 'admin' && (
              <button onClick={() => onDelete(comment.id)} className="font-medium text-slate-500 dark:text-slate-400 hover:text-red-600 dark:hover:text-red-500 transition">
                <FontAwesomeIcon icon={faTrashAlt} className="mr-1" /> Hapus
              </button>
            )}
          </div>
        )}
        {isReplying && (
          <div className="mt-4">
            <CommentForm
              mangaId={mangaId}
              chapterId={chapterId}
              parentId={comment.id}
              onCommentPosted={handleReplySuccess}
              onCancel={() => setIsReplying(false)}
              initialContent={`@${comment.user.name} `}
            />
          </div>
        )}
        <div className="mt-4 ml-8 space-y-4">
          {comment.replies.map(reply => (
            <CommentItem key={reply.id} comment={reply} mangaId={mangaId} chapterId={chapterId} onReplyPosted={onReplyPosted} onDelete={onDelete} />
          ))}
        </div>
      </div>
    </div>
  );
};

export default CommentItem;