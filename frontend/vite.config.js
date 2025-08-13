import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'path'

export default defineConfig({
  plugins: [vue()],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src'),
    },
  },
  server: {
    port: 8080,
    proxy: {
      // API rute
      '/api': {
        target: 'http://localhost:8091',
        changeOrigin: true,
        secure: false,
      },
      // Storage fajlovi (slike/PDF)
      '/storage': {
        target: 'http://localhost:8091',
        changeOrigin: true,
        secure: false,
      },
      // Sanctum & auth rute (važan deo!)
      '/sanctum': {
        target: 'http://localhost:8091',
        changeOrigin: true,
        secure: false,
      },
      '/login': {
        target: 'http://localhost:8091',
        changeOrigin: true,
        secure: false,
      },
      '/logout': {
        target: 'http://localhost:8091',
        changeOrigin: true,
        secure: false,
      },
      '/register': {
        target: 'http://localhost:8091',
        changeOrigin: true,
        secure: false,
      },
      // (opciono) zaboravljena lozinka i sl.
      '/password': {
        target: 'http://localhost:8091',
        changeOrigin: true,
        secure: false,
      },
    },
  },
})
