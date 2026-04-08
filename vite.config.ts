import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
  plugins: [react(), tailwindcss()],
  server: {
    proxy: {
      '/api': {
        target: 'https://neomangass.page.gd',
        changeOrigin: true,
        secure: false,
        followRedirects: true,
        headers: {
          'Accept': 'application/json',
        },
      },
      '/storage': {
        target: 'https://neomangass.page.gd',
        changeOrigin: true,
        secure: false,
      },
    },
  },
})
