import axios from 'axios'

// Ako postoji VITE_API_URL koristi njega, u suprotnom koristi origin + /api
const baseURL =
  import.meta.env.VITE_API_URL?.replace(/\/$/, '') ||
  `${window.location.origin.replace(/\/$/, '')}/api`;

const api = axios.create({
  baseURL, // npr: https://express-web.express/api
  withCredentials: false,
});

// automatski dodaj token ako postoji
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token');
  if (token) config.headers.Authorization = `Bearer ${token}`;
  return config;
});

export default api;
