import { Network } from '@capacitor/network';

export function setupNetwork() {
    Network.getStatus().then(status => updateUI(status.connected));
    Network.addListener('networkStatusChange', status => updateUI(status.connected));
}

function updateUI(connected) {
    const banner = document.getElementById('networkStatusBanner');
    if (!banner) return;

    if (!connected) {
        banner.querySelector('[data-role="message"]').textContent = 'Tidak ada koneksi internet';
        banner.className = 'network-banner network-banner--offline';
        banner.classList.remove('hidden');
        clearTimeout(banner._timer);
    } else {
        banner.querySelector('[data-role="message"]').textContent = 'Koneksi internet kembali normal';
        banner.className = 'network-banner network-banner--online';
        banner.classList.remove('hidden');
        clearTimeout(banner._timer);
        banner._timer = setTimeout(() => banner.classList.add('hidden'), 3000);
    }
}
