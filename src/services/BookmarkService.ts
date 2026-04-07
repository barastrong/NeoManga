import apiRoutes from '../routes/route';
import type{ Bookmark, PaginatedResponse } from '../types/manga';

export const getBookmarks = async (page: number = 1): Promise<PaginatedResponse<Bookmark>> => {
    const response = await apiRoutes.get(`/bookmarks?page=${page}`);
    return response.data;
};

export const removeBookmark = async (bookmarkId: number): Promise<{ message: string }> => {
    const response = await apiRoutes.delete(`/bookmarks/${bookmarkId}`);
    return response.data;
};

export const toggleBookmark = async (mangaSlug: string): Promise<{ is_bookmarked: boolean; followers_count: number }> => {
  const response = await apiRoutes.post(`/manga/${mangaSlug}/toggle-bookmark`);
  return response.data;
};