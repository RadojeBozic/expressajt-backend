export function isAuthenticated() {
  return !!localStorage.getItem('token');
}

export function getCurrentUser() {
  const user = localStorage.getItem('user');
  return user ? JSON.parse(user) : null;
}

export function logout() {
  localStorage.removeItem('token');
  localStorage.removeItem('user');
}
