import { App } from '@capacitor/app';
import { Dialog } from '@capacitor/dialog';

function isHomePage() {
    const path = window.location.pathname;
    return path === '/' || path === '/beranda' || path === '/login' || path === '';
}

export function setupBackButton() {
    App.addListener('backButton', async ({ canGoBack }) => {
        if (canGoBack && !isHomePage()) {
            window.history.back();
        } else {
            const { value } = await Dialog.confirm({
                title: 'Keluar dari GudangJateng',
                message: 'Apakah Anda yakin ingin keluar dari aplikasi?',
                okButtonTitle: 'Keluar',
                cancelButtonTitle: 'Batal',
            });
            if (value) {
                App.exitApp();
            }
        }
    });
}
