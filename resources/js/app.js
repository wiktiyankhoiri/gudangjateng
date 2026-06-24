import Alpine from 'alpinejs';
import persist from '@alpinejs/persist';
import TomSelect from 'tom-select';
import flatpickr from 'flatpickr';
import ApexCharts from 'apexcharts';
import { searchBar } from './components/search-bar';
import { notificationBell } from './components/notification-bell';

// Alpine
Alpine.plugin(persist);
Alpine.data('searchBar', searchBar);
Alpine.data('notificationBell', notificationBell);
window.Alpine = Alpine;
Alpine.start();

// Global libraries
window.TomSelect = TomSelect;
window.flatpickr = flatpickr;
window.ApexCharts = ApexCharts;

// Register PWA Service Worker
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
        navigator.serviceWorker.register('/sw.js');
    });
}

// Capacitor APK session extension — set cookie biar semua request terdeteksi
(function() {
    if (!document.cookie.includes('capacitor_app=1')) {
        // Cek User-Agent Capacitor
        if (navigator.userAgent.includes('Capacitor') || navigator.userAgent.includes('capacitor')) {
            document.cookie = 'capacitor_app=1; path=/; max-age=2592000'; // 30 hari
        }

        // Juga cek apakah running di WebView Android (capacitor ga selalu nambahin "Capacitor" di UA)
        if (/Android/i.test(navigator.userAgent) && !window.__PWA_ENABLED__) {
            // Cek apakah window.Capacitor ada (object yg disuntik Capacitor runtime)
            if (window.Capacitor || window.capacitor || navigator.userAgent.includes('wv')) {
                document.cookie = 'capacitor_app=1; path=/; max-age=2592000';
            }
        }
    }
})();

// Juga set header X-Capacitor buat request axios/fetch sebagai backup
if (window.axios?.defaults?.headers?.common) {
    if (document.cookie.includes('capacitor_app=1') || navigator.userAgent.includes('Capacitor')) {
        window.axios.defaults.headers.common['X-Capacitor'] = 'true';
    }
}
