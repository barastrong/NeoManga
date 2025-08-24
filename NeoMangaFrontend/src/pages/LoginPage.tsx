import React, { useState, type FormEvent } from 'react';
import { useNavigate, Link, useLocation } from 'react-router-dom';
import { login as loginService } from '../services/AuthServices';
import { useAuth } from '../context/AuthContext';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faEnvelope, faLock, faEye, faEyeSlash } from '@fortawesome/free-solid-svg-icons';

const LoginPage: React.FC = () => {
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [showPassword, setShowPassword] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [loading, setLoading] = useState(false);
    
    const navigate = useNavigate();
    const location = useLocation();
    const { setUser, setToken } = useAuth();

    const from = location.state?.from?.pathname;

    const handleSubmit = async (e: FormEvent) => {
        e.preventDefault();
        setError(null);
        setLoading(true);
        try {
            const { user, access_token } = await loginService({ email, password });
            localStorage.setItem('user', JSON.stringify(user));
            localStorage.setItem('authToken', access_token);
            setUser(user);
            setToken(access_token);
            navigate(from, { replace: true });
        } catch (err: any) {
            setError(err.response?.data?.message || 'Terjadi kesalahan. Silakan coba lagi.');
        } finally {
            setLoading(false);
        }
    };
    
    return (
        <div className="flex items-center justify-center pt-10 sm:pt-16">
            <div className="w-full max-w-md p-8 space-y-8 bg-white dark:bg-slate-800 rounded-2xl shadow-2xl shadow-slate-900/10 dark:shadow-slate-900/50">
                <div className="text-center">
                    <h2 className="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">
                        Welcome Back!
                    </h2>
                    <p className="mt-2 text-sm text-slate-500 dark:text-slate-400">
                        Login untuk melanjutkan petualanganmu.
                    </p>
                </div>
                <form className="mt-8 space-y-6" onSubmit={handleSubmit}>
                    <div className="space-y-4">
                        <div className="relative">
                            <span className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                                <FontAwesomeIcon icon={faEnvelope} />
                            </span>
                            <input
                                id="email-address" name="email" type="email" autoComplete="email" required
                                className="w-full pl-10 pr-4 py-3 bg-slate-100 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                placeholder="Alamat Email" value={email}
                                onChange={(e) => setEmail(e.target.value)}
                                disabled={loading} />
                        </div>
                        <div className="relative">
                             <span className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                                <FontAwesomeIcon icon={faLock} />
                            </span>
                            <input
                                id="password" name="password" type={showPassword ? 'text' : 'password'} autoComplete="current-password" required
                                className="w-full pl-10 pr-10 py-3 bg-slate-100 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                placeholder="Password" value={password}
                                onChange={(e) => setPassword(e.target.value)}
                                disabled={loading} />
                            <button type="button" onClick={() => setShowPassword(!showPassword)} className="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                                <FontAwesomeIcon icon={showPassword ? faEyeSlash : faEye} />
                            </button>
                        </div>
                    </div>
                    {error && (
                        <div className="p-3 bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-300 rounded-lg text-sm">
                            {error}
                        </div>
                    )}
                    <div>
                        <button type="submit" disabled={loading} className="w-full flex justify-center py-3 px-4 border border-transparent text-sm font-semibold rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 transition-all duration-300 transform hover:scale-105">
                            {loading ? 'Memproses...' : 'Login'}
                        </button>
                    </div>
                </form>
                <p className="text-center text-sm text-slate-500 dark:text-slate-400">
                    Belum punya akun?{' '}
                    <Link to="/register" className="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                        Buat Akun
                    </Link>
                </p>
            </div>
        </div>
    );
};

export default LoginPage;