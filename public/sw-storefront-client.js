const CACHE_NAME = 'fatihad-storefront-client-v1';

// Installation
self.addEventListener('install', function(event) {
    self.skipWaiting();
    console.log('SW Storefront Client installé');
});

// Activation
self.addEventListener('activate', function(event) {
    event.waitUntil(
        clients.claim().then(() => {
            console.log('SW Storefront Client activé');
        })
    );
});

// Stratégie : Network First
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
