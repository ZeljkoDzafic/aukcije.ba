const STATIC_CACHE = 'aukcijeba-static-v2';
const RUNTIME_CACHE = 'aukcijeba-runtime-v2';
const OFFLINE_FALLBACK = '/';

self.addEventListener('install', function (event) {
    event.waitUntil(
        caches.open(STATIC_CACHE).then(function (cache) {
            return cache.addAll([OFFLINE_FALLBACK, '/manifest.json']);
        }).then(function () {
            return self.skipWaiting();
        }),
    );
});

self.addEventListener('activate', function (event) {
    event.waitUntil(
        caches.keys().then(function (keys) {
            return Promise.all(
                keys
                    .filter(function (key) {
                        return ! [STATIC_CACHE, RUNTIME_CACHE].includes(key);
                    })
                    .map(function (key) {
                        return caches.delete(key);
                    }),
            );
        }).then(function () {
            return self.clients.claim();
        }),
    );
});

self.addEventListener('fetch', function (event) {
    const request = event.request;

    if (request.method !== 'GET') {
        return;
    }

    const url = new URL(request.url);

    if (url.origin !== self.location.origin) {
        return;
    }

    const isStaticAsset = /\.(?:js|css|png|jpg|jpeg|svg|webp|woff2?)$/i.test(url.pathname);

    if (isStaticAsset) {
        event.respondWith(
            caches.match(request).then(function (cachedResponse) {
                return cachedResponse || fetch(request).then(function (networkResponse) {
                    return caches.open(STATIC_CACHE).then(function (cache) {
                        cache.put(request, networkResponse.clone());
                        return networkResponse;
                    });
                });
            }),
        );

        return;
    }

    event.respondWith(
        fetch(request)
            .then(function (networkResponse) {
                return caches.open(RUNTIME_CACHE).then(function (cache) {
                    cache.put(request, networkResponse.clone());
                    return networkResponse;
                });
            })
            .catch(function () {
                return caches.match(request).then(function (cachedResponse) {
                    return cachedResponse || caches.match(OFFLINE_FALLBACK);
                });
            }),
    );
});
