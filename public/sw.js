// GudangJateng Service Worker (PWA Level 1 — Installable only)
// No offline cache, no background sync, no push notifications.

self.addEventListener('install', function (event) {
    self.skipWaiting();
});

self.addEventListener('activate', function (event) {
    event.waitUntil(self.clients.claim());
});
