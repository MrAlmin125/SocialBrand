// Service worker: lets the page open fast and show the last visit when offline (admin and forms are never cached)
const V = 'kaif-v2';
self.addEventListener('install', () => self.skipWaiting());
self.addEventListener('activate', e => e.waitUntil(caches.keys().then(ks => Promise.all(ks.filter(k => k !== V).map(k => caches.delete(k)))).then(() => self.clients.claim())));
self.addEventListener('fetch', e => {
  const r = e.request; if (r.method !== 'GET') return;
  const u = new URL(r.url); if (u.origin !== location.origin) return;
  if (/\/(admin\/|track\.php|ping\.php|geo\.php|contact\.php|review\.php|manifest\.php|favicon\.php)/.test(u.pathname)) return;
  if (r.mode === 'navigate') {
    e.respondWith(fetch(r).then(res => { const c = res.clone(); caches.open(V).then(ch => ch.put(r, c)); return res; }).catch(() => caches.match(r).then(m => m || caches.match('./'))));
    return;
  }
  if (/\.(css|js)$/.test(u.pathname)) {   // code: network first, cache only when offline
    e.respondWith(fetch(r).then(res => { if (res.ok) { const c = res.clone(); caches.open(V).then(ch => ch.put(r, c)); } return res; }).catch(() => caches.match(r)));
    return;
  }
  if (/\.(png|jpe?g|webp|svg|ico)$/.test(u.pathname) || /\/uploads\//.test(u.pathname) || /pwa-icon\.php/.test(u.pathname)) {
    e.respondWith(caches.match(r).then(m => { const f = fetch(r).then(res => { if (res.ok) { const c = res.clone(); caches.open(V).then(ch => ch.put(r, c)); } return res; }).catch(() => m); return m || f; }));
  }
});
