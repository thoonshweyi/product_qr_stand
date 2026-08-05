// import './bootstrap';
import '../css/app.css';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

window.pwaInstallPrompt = null;

window.addEventListener('beforeinstallprompt', (event) => {
    event.preventDefault();
    window.pwaInstallPrompt = event;
    window.dispatchEvent(new CustomEvent('pwa-install-available'));
});

window.addEventListener('appinstalled', () => {
    window.pwaInstallPrompt = null;
    window.dispatchEvent(new CustomEvent('pwa-install-complete'));
});

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch((error) => {
            console.error('Service worker registration failed:', error);
        });
    });
}
