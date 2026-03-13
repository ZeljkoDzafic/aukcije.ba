/**
 * ===================================
 * AUKCIJSKA PLATFORMA - MAIN JS ENTRY
 * ===================================
 */

import './bootstrap';
import '../css/app.css';

// Import Alpine.js for interactivity
import Alpine from 'alpinejs';

// Make Alpine available globally
window.Alpine = Alpine;

window.pwaInstallPrompt = function () {
    return {
        deferredPrompt: null,
        dismissed: localStorage.getItem('pwa_install_dismissed') === '1',

        init() {
            window.addEventListener('beforeinstallprompt', (event) => {
                event.preventDefault();
                this.deferredPrompt = event;
            });

            window.addEventListener('appinstalled', () => {
                this.deferredPrompt = null;
                localStorage.removeItem('pwa_install_dismissed');
            });
        },

        async install() {
            if (! this.deferredPrompt) {
                return;
            }

            this.deferredPrompt.prompt();
            await this.deferredPrompt.userChoice;
            this.deferredPrompt = null;
            this.dismissed = true;
        },

        dismiss() {
            this.dismissed = true;
            localStorage.setItem('pwa_install_dismissed', '1');
        },
    };
};

// Start Alpine
Alpine.start();

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(() => {});
    });
}

// Log application info
console.log('🎉 Aukcijska Platforma loaded successfully');
console.log('📦 Environment:', import.meta.env.MODE);
console.log('🔗 Echo configured:', typeof window.Echo !== 'undefined');
