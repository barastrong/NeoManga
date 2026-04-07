import { createContext, useState, useContext, useEffect, type ReactNode } from 'react';
import type { User } from '../types/manga';
import apiRoutes from '../routes/route';

const AUTH_TOKEN_KEY = 'authToken';
const AUTH_USER_KEY = 'user';
const AUTH_EXPIRES_AT_KEY = 'authExpiresAt';
const TOKEN_LIFETIME_MS = 20 * 24 * 60 * 60 * 1000; // 20 hari

interface AuthContextType {
    user: User | null;
    setUser: (user: User | null) => void;
    token: string | null;
    setToken: (token: string | null) => void;
    setAuthData: (user: User, token: string) => void;
    logout: () => void;
    isAuthenticated: boolean;
    isLoading: boolean;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export const AuthProvider = ({ children }: { children: ReactNode }) => {
    const [user, setUser] = useState<User | null>(null);
    const [token, setTokenState] = useState<string | null>(() => localStorage.getItem(AUTH_TOKEN_KEY));
    const [expiry, setExpiry] = useState<string | null>(() => localStorage.getItem(AUTH_EXPIRES_AT_KEY));
    const [isLoading, setIsLoading] = useState(true);

    const clearSession = () => {
        localStorage.removeItem(AUTH_TOKEN_KEY);
        localStorage.removeItem(AUTH_USER_KEY);
        localStorage.removeItem(AUTH_EXPIRES_AT_KEY);
        setUser(null);
        setTokenState(null);
        setExpiry(null);
        delete apiRoutes.defaults.headers.common['Authorization'];
    };

    const handleSetToken = (newToken: string | null) => {
        setTokenState(newToken);
        if (newToken) {
            localStorage.setItem(AUTH_TOKEN_KEY, newToken);
            apiRoutes.defaults.headers.common['Authorization'] = `Bearer ${newToken}`;
        } else {
            localStorage.removeItem(AUTH_TOKEN_KEY);
            delete apiRoutes.defaults.headers.common['Authorization'];
        }
    };

    const setAuthData = (newUser: User, newToken: string) => {
        const expiresAt = new Date(Date.now() + TOKEN_LIFETIME_MS).toISOString();
        localStorage.setItem(AUTH_USER_KEY, JSON.stringify(newUser));
        localStorage.setItem(AUTH_EXPIRES_AT_KEY, expiresAt);
        setUser(newUser);
        setExpiry(expiresAt);
        handleSetToken(newToken);
    };

    useEffect(() => {
        const storedToken = localStorage.getItem(AUTH_TOKEN_KEY);
        const storedUser = localStorage.getItem(AUTH_USER_KEY);
        const storedExpiry = localStorage.getItem(AUTH_EXPIRES_AT_KEY);

        if (storedExpiry && new Date(storedExpiry) <= new Date()) {
            clearSession();
            setIsLoading(false);
            return;
        }

        if (storedToken && storedUser && storedExpiry) {
            try {
                setUser(JSON.parse(storedUser));
                setExpiry(storedExpiry);
                handleSetToken(storedToken);
            } catch (error) {
                console.error('Gagal parse data user, membersihkan sesi.', error);
                clearSession();
            }
        }

        setIsLoading(false);
    }, []);

    useEffect(() => {
        if (!token || !expiry) return;

        const expiresAt = new Date(expiry).getTime();
        const now = Date.now();
        const timeRemaining = expiresAt - now;

        if (timeRemaining <= 0) {
            clearSession();
            return;
        }

        const timeoutId = window.setTimeout(() => {
            clearSession();
        }, timeRemaining);

        return () => {
            window.clearTimeout(timeoutId);
        };
    }, [token, expiry]);

    const logout = () => {
        clearSession();
    };

    const value = {
        user,
        setUser,
        token,
        setToken: handleSetToken,
        setAuthData,
        logout,
        isAuthenticated: !!token,
        isLoading,
    };

    return (
        <AuthContext.Provider value={value}>
            {children}
        </AuthContext.Provider>
    );
};

export const useAuth = () => {
    const context = useContext(AuthContext);
    if (context === undefined) {
        throw new Error('useAuth must be used within an AuthProvider');
    }
    return context;
};