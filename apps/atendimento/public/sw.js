/* Service Worker do totem/painel — cache de assets para funcionamento
   off-line parcial (Seção 2.5 "Operação Resiliente" / 9.1). */
'use strict';

var CACHE = 'sisrecepcao-atendimento-v1';

var ASSETS = [
    '/',
    '/totem',
    '/css/app.css',
    '/js/totem.js',
    '/js/painel.js',
    '/manifest.json',
];

self.addEventListener('install', function (event) {
    event.waitUntil(
        caches.open(CACHE).then(function (cache) {
            return cache.addAll(ASSETS);
        })
    );
    self.skipWaiting();
});

self.addEventListener('activate', function (event) {
    event.waitUntil(
        caches.keys().then(function (keys) {
            return Promise.all(keys.filter(function (k) { return k !== CACHE; }).map(function (k) { return caches.delete(k); }));
        })
    );
    self.clients.claim();
});

self.addEventListener('fetch', function (event) {
    var url = new URL(event.request.url);

    // Nunca cachemos mutações (POST) nem chamadas de API.
    if (event.request.method !== 'GET' || url.pathname.startsWith('/atendente') || url.pathname.startsWith('/painel/dados')) {
        return;
    }

    event.respondWith(
        fetch(event.request)
            .then(function (res) {
                var clone = res.clone();
                caches.open(CACHE).then(function (cache) { cache.put(event.request, clone); });
                return res;
            })
            .catch(function () {
                return caches.match(event.request).then(function (hit) {
                    return hit || caches.match('/');
                });
            })
    );
});
