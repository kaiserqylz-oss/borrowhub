const CACHE_NAME = 'borrowhub-static-v1';
const PRECACHE_URLS = [
    '/offline.html',
    '/manifest.webmanifest',
    '/images/icons/icon-192.png',
    '/images/icons/icon-512.png',
    '/images/icons/icon-maskable-512.png',
    '/images/icons/apple-touch-icon.png',
];
const BUILD_ASSET = /^\/build\/assets\/[A-Za-z0-9._-]+\.(?:css|js|woff2?)$/;
const ICON_ASSET = /^\/images\/icons\/[A-Za-z0-9._-]+\.png$/;

async function precacheApplicationAssets(cache) {
    await cache.addAll(PRECACHE_URLS);

    try {
        const response = await fetch('/build/manifest.json', { cache: 'no-store' });
        if (!response.ok) return;

        const manifest = await response.json();
        const assets = new Set();
        for (const entry of Object.values(manifest)) {
            if (entry.file) assets.add(`/build/${entry.file}`);
            for (const file of [...(entry.css || []), ...(entry.assets || [])]) {
                if (file) assets.add(`/build/${file}`);
            }
        }
        await Promise.all([...assets]
            .filter((path) => BUILD_ASSET.test(path))
            .map((path) => cache.add(path).catch(() => undefined)));
    } catch {
        // A missing build manifest should not prevent the offline page from installing.
    }
}

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(precacheApplicationAssets)
            .then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(keys
                .filter((key) => key.startsWith('borrowhub-static-') && key !== CACHE_NAME)
                .map((key) => caches.delete(key))))
            .then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    const request = event.request;
    if (request.method !== 'GET') return;

    const url = new URL(request.url);
    if (url.origin !== self.location.origin) return;

    if (request.mode === 'navigate') {
        event.respondWith(fetch(request).catch(async () => {
            const offlinePage = await caches.match('/offline.html');
            return offlinePage || new Response('You are offline. Connect to the internet and try again.', {
                status: 503,
                headers: { 'Content-Type': 'text/plain; charset=utf-8', 'Cache-Control': 'no-store' },
            });
        }));
        return;
    }

    if (url.pathname !== '/manifest.webmanifest' && url.pathname !== '/offline.html'
        && !ICON_ASSET.test(url.pathname) && !BUILD_ASSET.test(url.pathname)) return;

    event.respondWith(caches.match(request).then(async (cached) => {
        if (cached) return cached;

        const response = await fetch(request);
        if (response.ok && response.status === 200 && response.type === 'basic') {
            const cache = await caches.open(CACHE_NAME);
            await cache.put(request, response.clone());
        }
        return response;
    }));
});
