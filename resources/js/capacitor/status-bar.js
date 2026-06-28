import { StatusBar, Style } from '@capacitor/status-bar';

export async function setupStatusBar() {
    await StatusBar.setStyle({ style: Style.Light });
    await StatusBar.setBackgroundColor({ color: '#0A1633' });
    await StatusBar.setOverlaysWebView({ overlay: false });
}
