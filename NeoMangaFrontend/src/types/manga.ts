export interface Chapter {
  id: number;
  number: string;
  slug: string;
  created_at: string;
  status?: string;
}

export interface Genre {
  id: number;
  name: string;
  slug: string;
}

export interface User {
  id: number;
  name: string;
  email: string;
  role: string;
}

export interface HistoryItem {
  id: number;
  updated_at: string;
  chapter: Chapter;
}

export interface Comment {
  id: number;
  content: string;
  created_at: string;
  likes_count: number;
  user: User;
  replies: Comment[];
  parent_id: number | null;
  is_liked_by_user?: boolean; 
}

export interface MangaItem {
  id: number;
  slug: string;
  title: string;
  cover_image: string;
  ratings_avg_rating?: number;
  status?: 'ongoing' | 'completed' | 'hiatus';
  type?: 'manga' | 'manhwa' | 'manhua';
  latest_published_chapter?: Chapter;
}

export interface MangaDetail extends Omit<MangaItem, 'latest_published_chapter'> {
  alternative_title?: string;
  description: string;
  author: string;
  artist: string;
  created_at: string;
  updated_at: string;
  user: User;
  genres: Genre[];
  followers_count: number;
  comments: Comment[];
}

export interface History {
  id: number;
  manga: MangaItem;
  chapter: Chapter;
  created_at: string;
  updated_at: string;
}

export interface PaginatedResponse<T> {
  current_page: number;
  data: T[];
  first_page_url: string;
  from: number;
  last_page: number;
  last_page_url: string;
  links: {
    url: string | null;
    label: string;
    active: boolean;
  }[];
  next_page_url: string | null;
  path: string;
  per_page: number;
  prev_page_url: string | null;
  to: number;
  total: number;
}

export interface Bookmark {
  id: number;
  created_at: string;
  manga: MangaItem;
}

export interface ChapterNavigation {
  id: number;
  slug: string;
  number: string;
}

export interface ChapterDetail extends Chapter {
  manga: MangaItem;
  chapter_images: string[];
}