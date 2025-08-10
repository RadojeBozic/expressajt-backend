// vite.config.js
import { defineConfig, loadEnv } from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'path'

export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd(), '') // čita .env.* fajlove
  const isProd = mode === 'production'

  return {
    plugins: [vue()],
    resolve: {
      alias: {
        '@': path.resolve(__dirname, './src'),
      },
    },
    server: {
      host: true,         // ili 'localhost'; true sluša na svim interfejsima
      port: 5173,
      strictPort: true,
      open: false,
      // ✅ Lokalni proxy: koristi relativne rute i u dev-u
      proxy: {
        '/api': {
          target: 'http://localhost:8080',
          changeOrigin: true,
        },
        // često korisno i za assete iz backenda
        '/storage': {
          target: 'http://localhost:8080',
          changeOrigin: true,
        },
      },
    },
    // Nema potrebe za define: { 'process.env': ... }
    // Vite koristi import.meta.env (npr. import.meta.env.VITE_API_URL)

    build: {
      sourcemap: !isProd, // sourcemap u dev/test, bez u production
      outDir: 'dist',
      emptyOutDir: true,
    },
    // base: '/', // ostavi default; postavi ako deployuješ pod pod-putanjom
  }
})
