import apiRoutes from '../routes/route';
import type { Comment } from '../types/manga';

export const getCommentsForChapter = async (chapterId: number): Promise<Comment[]> => {
  const response = await apiRoutes.get(`/chapter/${chapterId}/comments`);
  return response.data.data;
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
    
    console.log('Comment posted successfully:', response.data);
    console.log(`✅ Comment berhasil diposting di manga "${response.data.manga?.title || 'Unknown'}" chapter ${response.data.chapter?.number || 'Unknown'}`);
    return response.data;
  } catch (error: any) {
    console.error('Comment post error:', {
      status: error.response?.status,
      data: error.response?.data,
      message: error.message,
      errors: error.response?.data?.errors,
      fullResponse: error.response
    });
    
    if (error.response?.data?.errors) {
      console.error('Validation errors:', error.response.data.errors);
    }
    
    throw error;
  }
};

export const toggleLike = async (commentId: number): Promise<{ liked: boolean; likes_count: number }> => {
  const response = await apiRoutes.post(`/comments/${commentId}/like`);
  return response.data;
};

export const deleteComment = async (commentId: number): Promise<void> => {
  await apiRoutes.delete(`/comments/${commentId}`);
};