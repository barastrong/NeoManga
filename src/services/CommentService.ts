import apiRoutes from '../routes/route';
import type { Comment } from '../types/manga';
import { AxiosError } from 'axios';

export const getCommentsForChapter = async (chapterId: number): Promise<Comment[]> => {
  const response = await apiRoutes.get(`/chapter/${chapterId}/comments`);
  return response.data.data || response.data;
};

export const postComment = async (
  mangaId: number,
  chapterId: number,
  content: string,
  parentId?: number
): Promise<Comment> => {
  try {
    console.log('Posting comment with data:', {
      manga_id: mangaId,
      chapter_id: chapterId,
      content,
      parent_id: parentId,
    });
    
    const response = await apiRoutes.post('/comments', {
      manga_id: mangaId,
      chapter_id: chapterId,
      content,
      parent_id: parentId,
    });
    
    console.log(`✅ Comment berhasil diposting!`);
    return response.data;
  } catch (error) {
    const axiosError = error as AxiosError;
    console.error('Comment post error:', {
      status: axiosError.response?.status,
      data: axiosError.response?.data,
      message: axiosError.message,
      errors: (axiosError.response?.data as { errors?: unknown })?.errors,
      fullResponse: axiosError.response
    });
    
    if ((axiosError.response?.data as { errors?: unknown })?.errors) {
      console.error('Validation errors:', (axiosError.response?.data as { errors?: unknown }).errors);
    }
    
    throw error;
  }
};

export const toggleLike = async (commentId: number): Promise<{ liked: boolean; likes_count: number }> => {
  try {
    const response = await apiRoutes.post(`/comments/${commentId}/like`);
    return response.data;
  } catch (error) {
    const axiosError = error as AxiosError;
    console.error('Like toggle error:', axiosError.response?.data);
    throw error;
  }
};

export const deleteComment = async (commentId: number): Promise<void> => {
  try {
    await apiRoutes.delete(`/comments/${commentId}`);
  } catch (error) {
    const axiosError = error as AxiosError;
    console.error('Delete comment error:', axiosError.response?.data);
    throw error;
  }
};