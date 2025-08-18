import axios from 'axios'

/**
 * BAZE iz okruženja
 * - U dev-u koristimo Vite proxy (API ostaje relativan: /api)
 * - U prod-u je takođe /api (backend servira build)
 * - WEB_BASE služi za /sanctum/csrf-cookie, /login, /logout…
 */
// --- BEGIN base URLs ---
const RUNTIME_API = typeof window !== 'undefined' && window.__API_BASE_URL
const RUNTIME_WEB = typeof window !== 'undefined' && window.__WEB_BASE_URL
const isLocal = typeof window !== 'undefined' && /(localhost|127\.0\.0\.1)/.test(window.location.hostname)

const API_BASE =
  RUNTIME_API ||
  import.meta.env.VITE_API_URL ||
  (isLocal ? 'http://localhost:8000/api' : '/api')

const WEB_BASE =
  RUNTIME_WEB ||
  import.meta.env.VITE_WEB_BASE_URL ||
  (isLocal ? 'http://localhost:8000' : '/')

export const web = axios.create({
  baseURL: WEB_BASE,
  withCredentials: true,
  headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
})

export const api = axios.create({
  baseURL: API_BASE,
  withCredentials: true,
  headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
})

// CSRF header iz cookie-ja (ako postoji)
function attachXsrf(config) {
  try {
    const m = document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]+)/)
    if (m) config.headers['X-XSRF-TOKEN'] = decodeURIComponent(m[1])
  } catch {}
  return config
}
web.interceptors.request.use(attachXsrf)
api.interceptors.request.use(attachXsrf)

export function getCsrfCookie() {
  return web.get('/sanctum/csrf-cookie')
}

// Dev logger
const DEV = import.meta.env.DEV === true
for (const client of [web, api]) {
  client.interceptors.response.use(
    r => { if (DEV) console.log(`✅ ${r.config.method?.toUpperCase()} ${r.config.url} → ${r.status}`); return r },
    e => { if (DEV) console.error(`❌ ${e.config?.method?.toUpperCase()} ${e.config?.url} → ${e.response?.status}`); return Promise.reject(e) }
  )
}
// --- END base URLs ---



/** Shared defaults */
const commonHeaders = {
  Accept: 'application/json',
  'X-Requested-With': 'XMLHttpRequest',
  // (opciono) prosledi jezik browsera backendu
  'Accept-Language': typeof navigator !== 'undefined' ? navigator.language : 'en',
}




/** Sanctum CSRF cookie/header imena */
for (const client of [web, api]) {
  client.defaults.xsrfCookieName = 'XSRF-TOKEN'
  client.defaults.xsrfHeaderName = 'X-XSRF-TOKEN'
}

/** Ručno ubaci X-XSRF-TOKEN iz cookie-ja (decode-ovan) – pomaže u edge slučajevima */
function attachXsrf(config) {
  try {
    const m = document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]+)/)
    if (m) config.headers['X-XSRF-TOKEN'] = decodeURIComponent(m[1])
  } catch {}
  return config
}
web.interceptors.request.use(attachXsrf)
api.interceptors.request.use(attachXsrf)

/** Lagani dev loger (bez spama u production-u) */
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

      // 419 = CSRF token mismatch / session expired → povuci novi csrf cookie i probaj JEDNOM ponovo
      const status = error?.response?.status
      const cfg = error?.config
      const isSafeToRetry =
        status === 419 && cfg && !cfg.__csrfRetried && (cfg.baseURL?.includes('/api') || cfg.baseURL?.endsWith('/'))

      if (isSafeToRetry) {
        try {
          await getCsrfCookie()
          cfg.__csrfRetried = true
          return (cfg.baseURL === API_BASE ? api : web).request(cfg)
        } catch (e) {
          // padamo na originalnu grešku
        }
      }

      return Promise.reject(error)
    }
  )
}

/** Učitaj CSRF cookie (Sanctum) pre POST/PUT/DELETE na auth rutama */
export function getCsrfCookie() {
  // mora na WEB domenu, ne /api
  return web.get('/sanctum/csrf-cookie', { withCredentials: true })
}

/** Helper: bezbedno čitanje /api/user (401 tretiraj kao guest) */
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
