import React, { createContext, useState, useContext, useEffect, ReactNode } from 'react';
import type { User } from '../types/manga';
import apiRoutes from '../routes/route';

interface AuthContextType {
    user: User | null;
    setUser: (user: User | null) => void;
    token: string | null;
    setToken: (token: string | null) => void;
    logout: () => void;
    isAuthenticated: boolean;
    isLoading: boolean;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export const AuthProvider = ({ children }: { children: ReactNode }) => {
    const [user, setUser] = useState<User | null>(null);
    const [token, setToken] = useState<string | null>(() => localStorage.getItem('authToken'));
    const [isLoading, setIsLoading] = useState(true);

    useEffect(() => {
        const storedToken = localStorage.getItem('authToken');
        const storedUser = localStorage.getItem('user');

        if (storedToken && storedUser) {
            try {
                setUser(JSON.parse(storedUser));
                setToken(storedToken);
                apiRoutes.defaults.headers.common['Authorization'] = `Bearer ${storedToken}`;
            } catch (error) {
                console.error("Gagal parse data user, membersihkan sesi.", error);
                localStorage.clear();
            }
        }
        setIsLoading(false);
    }, []);

    const handleSetToken = (newToken: string | null) => {
        setToken(newToken);
        if (newToken) {
            localStorage.setItem('authToken', newToken);
            apiRoutes.defaults.headers.common['Authorization'] = `Bearer ${newToken}`;
        } else {
            localStorage.removeItem('authToken');
            delete apiRoutes.defaults.headers.common['Authorization'];
        }
    };

    const logout = () => {
        localStorage.removeItem('authToken');
        localStorage.removeItem('user');
        setUser(null);
        handleSetToken(null);
    };

    const value = {
        user,
        setUser,
        token,
        setToken: handleSetToken,
        logout,
        isAuthenticated: !!token,
        isLoading,
    };

    return (
        <AuthContext.Provider value={value}>
            {!isLoading && children}
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