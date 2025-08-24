import apiRoutes from '../routes/route';
import  type { User } from '../types/manga';

interface LoginCredentials {
  email: string;
  password: string;
}

interface RegisterData extends LoginCredentials {
  name: string;
  password_confirmation: string;
}

interface AuthResponse {
  access_token: string;
  user: User;
}

export const login = async (credentials: LoginCredentials): Promise<AuthResponse> => {
  const response = await apiRoutes.post<AuthResponse>('/login', credentials);
  return response.data;
};

export const register = async (data: RegisterData): Promise<AuthResponse> => {
    const response = await apiRoutes.post<AuthResponse>('/register', data);
    return response.data;
};