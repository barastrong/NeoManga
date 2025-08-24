import React, { useEffect } from 'react';
import { Link, useLocation } from 'react-router-dom';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faTimes, faSignInAlt, faUserPlus } from '@fortawesome/free-solid-svg-icons';

interface LoginPromptModalProps {
  isOpen: boolean;
  onClose: () => void;
}

const LoginPromptModal: React.FC<LoginPromptModalProps> = ({ isOpen, onClose }) => {
  const location = useLocation();

  useEffect(() => {
    const handleEsc = (event: KeyboardEvent) => {
      if (event.key === 'Escape') onClose();
    };
    if (isOpen) {
      window.addEventListener('keydown', handleEsc);
      document.body.style.overflow = 'hidden';
    }
    return () => {
      window.removeEventListener('keydown', handleEsc);
      document.body.style.overflow = 'auto';
    };
  }, [isOpen, onClose]);

  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-4" onClick={onClose}>
      <div className="bg-white dark:bg-slate-800 rounded-lg shadow-xl max-w-md w-full mx-4 transform transition-all" onClick={(e) => e.stopPropagation()}>
        <div className="flex items-center justify-between p-6 border-b border-slate-200 dark:border-slate-700">
          <h3 className="text-xl font-semibold text-slate-900 dark:text-white">
            Login Diperlukan
          </h3>
          <button onClick={onClose} className="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
            <FontAwesomeIcon icon={faTimes} className="text-xl" />
          </button>
        </div>
        <div className="p-6 text-center">
            <p className="text-slate-600 dark:text-slate-400 text-sm">
                Anda harus login untuk menggunakan fitur ini. Bergabunglah dengan komunitas untuk menyimpan manga favorit Anda!
            </p>
        </div>
        <div className="flex flex-col sm:flex-row gap-3 p-6 border-t border-slate-200 dark:border-slate-700">
            <Link to="/login" state={{ from: location }} className="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white text-center font-medium py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                <FontAwesomeIcon icon={faSignInAlt} /> Login
            </Link>
            <Link to="/register" state={{ from: location }} className="flex-1 bg-slate-600 hover:bg-slate-700 text-white text-center font-medium py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                <FontAwesomeIcon icon={faUserPlus} /> Daftar
            </Link>
        </div>
      </div>
    </div>
  );
};

export default LoginPromptModal;