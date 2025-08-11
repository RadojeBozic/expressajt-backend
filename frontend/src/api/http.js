import axios from 'axios'

/** WEB klijent (za /login, /logout, /register, /sanctum/csrf-cookie) */
export const web = axios.create({
  baseURL: '/',                 // bez /api
  withCredentials: true,        // šalje cookie
})

/** API klijent (za /api/* pozive) */
export const api = axios.create({
  baseURL: '/api',
  withCredentials: true,
})

/** Zajednički default-i */
for (const client of [web, api]) {
  client.defaults.xsrfCookieName = 'XSRF-TOKEN'
  client.defaults.xsrfHeaderName = 'X-XSRF-TOKEN'
  client.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest' // sprečava HTML redirect
  client.defaults.headers.common['Accept'] = 'application/json'         // traži JSON
}

/** 💪 Ručno ubaci decode-ovan XSRF iz cookie-ja – rešava 419 */
function attachXsrf(config) {
  try {
    const m = document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]+)/)
    if (m) config.headers['X-XSRF-TOKEN'] = decodeURIComponent(m[1])
  } catch {}
  return config
}
web.interceptors.request.use(attachXsrf)
api.interceptors.request.use(attachXsrf)

/** Uvek prvo CSRF (isti origin, web klijent) */
export function getCsrfCookie() {
  return web.get('/sanctum/csrf-cookie')
}

export default api
