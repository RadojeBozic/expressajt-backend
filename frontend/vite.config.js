import { defineConfig, loadEnv } from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'path'

export default defineConfig(({ mode }) => {
  // U .env.development možeš zadati:
  // VITE_BACKEND_ORIGIN=http://localhost:8000
  // VITE_DEV_PORT=8080
  // VITE_PREVIEW_PORT=4173
  const env = loadEnv(mode, process.cwd(), '')
  const backend = env.VITE_BACKEND_ORIGIN || 'http://localhost:8000'
  const devPort = Number(env.VITE_DEV_PORT || 8080)
  const previewPort = Number(env.VITE_PREVIEW_PORT || 4173)

  return {
    plugins: [vue()],
    resolve: {
      alias: {
        '@': path.resolve(__dirname, './src'),
      },
    },
    server: {
      port: devPort,
      strictPort: false,
      proxy: {
        // API rute
        '/api': { target: backend, changeOrigin: true, secure: false },
        // Storage fajlovi (slike/PDF)
        '/storage': { target: backend, changeOrigin: true, secure: false },
        // Sanctum & auth rute
        '/sanctum': { target: backend, changeOrigin: true, secure: false },
        '/login':   { target: backend, changeOrigin: true, secure: false },
        '/logout':  { target: backend, changeOrigin: true, secure: false },
        '/register':{ target: backend, changeOrigin: true, secure: false },
        '/password':{ target: backend, changeOrigin: true, secure: false },
      },
    },
    // Korisno i za `vite preview`
    preview: {
      port: previewPort,
      proxy: {
        '/api':      { target: backend, changeOrigin: true, secure: false },
        '/storage':  { target: backend, changeOrigin: true, secure: false },
        '/sanctum':  { target: backend, changeOrigin: true, secure: false },
        '/login':    { target: backend, changeOrigin: true, secure: false },
        '/logout':   { target: backend, changeOrigin: true, secure: false },
        '/register': { target: backend, changeOrigin: true, secure: false },
        '/password': { target: backend, changeOrigin: true, secure: false },
      },
    },
    build: {
      sourcemap: mode !== 'production',
      outDir: 'dist',
      emptyOutDir: true,
    },
  }
})
