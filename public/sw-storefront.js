const CACHE_NAME = 'FatiHad-storefront-v1';
const urlsToCache = [
    '/login',
    '/',
    '/css/app.css',
    '/js/app.js',
    '/manifest-storefront.json'
];

// Installation
self.addEventListener('install', function(event) {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(function(cache) {
                console.log('Cache Storefront ouvert');
                return cache.addAll(urlsToCache);
            })
    );
});

// Activation
self.addEventListener('activate', function(event) {
    event.waitUntil(
        caches.keys().then(function(cacheNames) {
            return Promise.all(
                cacheNames.map(function(cacheName) {
                    if (cacheName !== CACHE_NAME) {
                        console.log('Suppression ancien cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});

// Stratégie : Network First, puis cache
self.addEventListener('fetch', function(event) {
    event.respondWith(
        fetch(event.request)
            .then(function(response) {
                if (response.status === 200) {
                    const responseClone = response.clone();
                    caches.open(CACHE_NAME).then(function(cache) {
                        cache.put(event.request, responseClone);
                    });
                }
                return response;
            })
            .catch(function() {
                return caches.match(event.request);
            })
    );
});
