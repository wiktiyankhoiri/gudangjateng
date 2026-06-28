import { Capacitor } from '@capacitor/core';
import config from './config';
import { setupSplash } from './splash';
import { setupStatusBar } from './status-bar';
import { setupBackButton } from './back-button';
import { setupNetwork } from './network';

export function initializeCapacitor() {
    if (!Capacitor.isNativePlatform()) return;

    if (config.enableSplash) setupSplash();
    if (config.enableStatusBar) setupStatusBar();
    if (config.enableBackButton) setupBackButton();
    if (config.enableNetwork) setupNetwork();
}
