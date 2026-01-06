import { BrowserRouter, Routes, Route, Outlet } from 'react-router-dom';
import { AuthProvider } from './context/AuthContext';
import Navbar from './components/navbar';
import HomePage from './pages/index';
import MangaList from './pages/MangaList';
import MangShow from './pages/Show';
import HistoryPage from './pages/History';
import LoginPage from './pages/LoginPage';
import RegisterPage from './pages/RegisterPage';
import BookmarkPage from './pages/Bookmark';
import ChapterPage from './pages/Chapter';
import Snowfall from 'react-snowfall'

const MainLayout = () => {
  return (
    <div className="bg-white dark:bg-slate-900 min-h-screen text-gray-800 dark:text-gray-200 font-sans transition-colors duration-300">
      <Snowfall />
      <Navbar />
      <main className="container mx-auto px-4 py-8 md:px-6 md:py-10">
        <Outlet />
      </main>
    </div>
  );
};

function App() {
  return (
    <AuthProvider>
      <BrowserRouter>
        <Routes>
          <Route path="/" element={<MainLayout />}>
            <Route index element={<HomePage />} />
            <Route path="manga-list" element={<MangaList />} />
            <Route path="manga/:slug" element={<MangShow />} />
            <Route path="login" element={<LoginPage />} />
            <Route path="register" element={<RegisterPage />} />
            <Route path="history" element={<HistoryPage />} />
            <Route path="bookmark" element={<BookmarkPage />} />
            <Route path="chapter/:slug" element={<ChapterPage />} />
          </Route>
        </Routes>
      </BrowserRouter>
    </AuthProvider>
  );
}

export default App;