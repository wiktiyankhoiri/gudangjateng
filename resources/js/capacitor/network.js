import { Network } from '@capacitor/network';

let _wasOffline = false;

export function setupNetwork() {
    Network.getStatus().then(status => updateUI(status.connected, false));
    Network.addListener('networkStatusChange', status => updateUI(status.connected, true));
}

function updateUI(connected, isChange) {
    const banner = document.getElementById('networkStatusBanner');
    if (!banner) return;

    if (!connected) {
        _wasOffline = true;
        banner.querySelector('[data-role="message"]').textContent = 'Tidak ada koneksi internet';
        banner.className = 'network-banner network-banner--offline';
        banner.classList.remove('hidden');
        clearTimeout(banner._timer);
        return;
    }

    if (!_wasOffline) {
        banner.classList.add('hidden');
        return;
    }

    _wasOffline = false;
    banner.querySelector('[data-role="message"]').textContent = 'Koneksi internet kembali normal';
    banner.className = 'network-banner network-banner--online';
    banner.classList.remove('hidden');
    clearTimeout(banner._timer);
    banner._timer = setTimeout(() => banner.classList.add('hidden'), 3000);
}
