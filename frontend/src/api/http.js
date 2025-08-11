import axios from 'axios'

/** WEB klijent (za /sanctum/csrf-cookie, /login, /logout, /register) */
export const web = axios.create({ baseURL: '/',  withCredentials: true })

/** API klijent (za /api/* rute) */
export const api = axios.create({ baseURL: '/api', withCredentials: true })

for (const client of [web, api]) {
  client.defaults.xsrfCookieName = 'XSRF-TOKEN'
  client.defaults.xsrfHeaderName = 'X-XSRF-TOKEN'
  client.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
  client.defaults.headers.common['Accept'] = 'application/json'
}

/** Ručno ubacimo X-XSRF-TOKEN iz cookie-ja (decode-ovan) */
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

export default api