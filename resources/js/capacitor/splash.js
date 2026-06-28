import { SplashScreen } from '@capacitor/splash-screen';

export async function setupSplash() {
    if (document.readyState === 'complete') {
        await SplashScreen.hide();
    } else {
        window.addEventListener('load', async () => {
            await SplashScreen.hide();
        });
    }
}
