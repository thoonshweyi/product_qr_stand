const CACHE_NAME = 'product-qr-stand-v1';
const APP_ASSETS = [
    '/site.webmanifest',
    '/pwa/icon-192.png',
    '/pwa/icon-512.png',
    '/pwa/icon-maskable-512.png',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => cache.addAll(APP_ASSETS))
            .then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(
                keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))
            ))
            .then(() => self.clients.claim())
    );
});

// Keep authenticated pages network-only so private dashboard responses are not cached.
self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') {
        return;
    }

    const url = new URL(event.request.url);

    if (url.origin === self.location.origin && APP_ASSETS.includes(url.pathname)) {
        event.respondWith(
            caches.match(event.request).then((response) => response || fetch(event.request))
        );
    }
});
