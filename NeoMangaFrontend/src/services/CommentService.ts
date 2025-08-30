import apiRoutes from '../routes/route';
import type { Comment } from '../types/manga';

export const getCommentsForChapter = async (chapterId: number): Promise<Comment[]> => {
  const response = await apiRoutes.get(`/chapters/${chapterId}/comments`);
  return response.data.data;
};

export const postComment = async (
  mangaId: number,
  chapterId: number,
  content: string,
  parentId?: number
): Promise<Comment> => {
  const response = await apiRoutes.post('/comments', {
    manga_id: mangaId,
    chapter_id: chapterId,
    content,
    parent_id: parentId,
  });
  return response.data;
};

export const toggleLike = async (commentId: number): Promise<{ liked: boolean; likes_count: number }> => {
  const response = await apiRoutes.post(`/comments/${commentId}/like`);
  return response.data;
};

export const deleteComment = async (commentId: number): Promise<void> => {
  await apiRoutes.delete(`/comments/${commentId}`);
};