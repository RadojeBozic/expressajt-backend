// src/api/http.js
import axios from 'axios'

// ------------------------------
// BASE URL konfiguracija
// ------------------------------
const isBrowser = typeof window !== 'undefined'
const isLocal = isBrowser && /(localhost|127\.0\.0\.1)/.test(window.location.hostname)

const RUNTIME_API = isBrowser && window.__API_BASE_URL
const RUNTIME_WEB = isBrowser && window.__WEB_BASE_URL

// Lokalno → pretpostavi Laravel na :8000; u prod → relativno /api
const API_BASE =
  RUNTIME_API ||
  import.meta.env?.VITE_API_URL ||
  (isLocal ? 'http://localhost:8000/api' : '/api')

const WEB_BASE =
  RUNTIME_WEB ||
  import.meta.env?.VITE_WEB_BASE_URL ||
  (isLocal ? 'http://localhost:8000' : '/')

// ------------------------------
// Axios instance-i
// ------------------------------
export const web = axios.create({
  baseURL: WEB_BASE,
  withCredentials: true,
  headers: {
    Accept: 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
    'Accept-Language': isBrowser ? (navigator.language || 'en') : 'en',
  },
})

export const api = axios.create({
  baseURL: API_BASE,
  withCredentials: true,
  headers: {
    Accept: 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
    'Accept-Language': isBrowser ? (navigator.language || 'en') : 'en',
  },
})

// Sanctum cookie/header imena
for (const client of [web, api]) {
  client.defaults.xsrfCookieName = 'XSRF-TOKEN'
  client.defaults.xsrfHeaderName = 'X-XSRF-TOKEN'
}

// ------------------------------
// Helperi
// ------------------------------

// Učitaj CSRF cookie (uvek preko WEB_BASE)
export function getCsrfCookie() {
  return web.get('/sanctum/csrf-cookie', { withCredentials: true })
}

// Ručno ubaci X-XSRF-TOKEN iz cookie-ja (decode-ovan)
function attachXsrf(config) {
  try {
    if (isBrowser) {
      const m = document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]+)/)
      if (m) config.headers['X-XSRF-TOKEN'] = decodeURIComponent(m[1])
    }
  } catch {}
  return config
}
web.interceptors.request.use(attachXsrf)
api.interceptors.request.use(attachXsrf)

// Lagani dev-logger + 419 retry
const DEV = import.meta.env?.DEV === true
for (const client of [web, api]) {
  client.interceptors.response.use(
    (response) => {
      if (DEV) {
        try {
          const m = response.config.method?.toUpperCase()
          const u = response.config.url
          // eslint-disable-next-line no-console
          console.log(`✅ ${m} ${u} → ${response.status}`)
        } catch {}
      }
      return response
    },
    async (error) => {
      if (DEV) {
        try {
          const m = error.config?.method?.toUpperCase()
          const u = error.config?.url
          const st = error.response?.status
          // eslint-disable-next-line no-console
          console.error(`❌ ${m} ${u} → ${st}`)
        } catch {}
      }

      // 419 = CSRF mismatch / session expired → povuci novi CSRF i probaj JEDNOM ponovo
      const status = error?.response?.status
      const cfg = error?.config
      const canRetry =
        status === 419 && cfg && !cfg.__csrfRetried &&
        (cfg.baseURL?.includes('/api') || cfg.baseURL?.endsWith('/'))

      if (canRetry) {
        try {
          await getCsrfCookie()
          cfg.__csrfRetried = true
          return (cfg.baseURL === API_BASE ? api : web).request(cfg)
        } catch {
          // pada na originalnu grešku ispod
        }
      }

      return Promise.reject(error)
    }
  )
}

// Bezbedno čitanje ulogovanog user-a (401 → null)
export async function getCurrentUserSafe() {
  try {
    const { data } = await api.get('/user')
    return data
  } catch (e) {
    if (e?.response?.status === 401) return null
    throw e
  }
}

export default api
