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

// Capacitor APK session extension header
if (navigator.userAgent.includes('Capacitor')) {
    // Send header so middleware can detect Capacitor requests
    window.axios?.defaults?.headers?.common && (
        window.axios.defaults.headers.common['X-Capacitor'] = 'true'
    );
}
