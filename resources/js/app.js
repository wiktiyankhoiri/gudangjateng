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
