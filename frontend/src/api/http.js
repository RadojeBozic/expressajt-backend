import axios from 'axios'

/**
 * BAZE iz okruženja
 * - U dev-u koristimo Vite proxy pa neka API ostane relativan (/api)
 * - U prod-u je isto /api (backend servira build)
 */
const API_BASE = import.meta.env.VITE_API_URL || '/api'
const WEB_BASE = import.meta.env.VITE_WEB_BASE_URL || '/'

/** WEB klijent (sanctum, login, logout, register, password reset…) */
export const web = axios.create({
  baseURL: WEB_BASE,
  withCredentials: true,
  headers: {
    Accept: 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  },
})

/** API klijent (za /api/* rute) */
export const api = axios.create({
  baseURL: API_BASE,
  withCredentials: true,
  headers: {
    Accept: 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  },
})

/** Sanctum CSRF cookie/header imena */
for (const client of [web, api]) {
  client.defaults.xsrfCookieName = 'XSRF-TOKEN'
  client.defaults.xsrfHeaderName = 'X-XSRF-TOKEN'
}

/** Ručno ubacimo X-XSRF-TOKEN iz cookie-ja (decode-ovan) – pomaže u edge slučajevima */
function attachXsrf(config) {
  try {
    const m = document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]+)/)
    if (m) config.headers['X-XSRF-TOKEN'] = decodeURIComponent(m[1])
  } catch {}
  return config
}
web.interceptors.request.use(attachXsrf)
api.interceptors.request.use(attachXsrf)

/** Učitaj CSRF cookie (Sanctum) pre POST/PUT/DELETE ako si na auth rutama */
export function getCsrfCookie() {
  return web.get('/sanctum/csrf-cookie')
}

/** Lagani dev loger (bez spama u production-u) */
const DEV = import.meta.env.DEV === true
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
    (error) => {
      if (DEV) {
        try {
          const m = error.config?.method?.toUpperCase()
          const u = error.config?.url
          const st = error.response?.status
          // eslint-disable-next-line no-console
          console.error(`❌ ${m} ${u} → ${st}`)
        } catch {}
      }
      return Promise.reject(error)
    }
  )
}

export default api
