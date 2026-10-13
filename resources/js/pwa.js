const offlineNotice = document.querySelector('[data-offline-notice]');
const installButtons = document.querySelectorAll('[data-install-app]');
const serviceWorkerUrl = document.querySelector('meta[name="service-worker-url"]')?.content;
let installPrompt = null;

function updateNetworkNotice() {
    if (offlineNotice) offlineNotice.hidden = navigator.onLine;
}

updateNetworkNotice();
window.addEventListener('online', updateNetworkNotice);
window.addEventListener('offline', updateNetworkNotice);

document.addEventListener('submit', (event) => {
    if (navigator.onLine) return;

    event.preventDefault();
    if (offlineNotice) {
        offlineNotice.hidden = false;
        offlineNotice.focus();
    }
}, true);

if ('serviceWorker' in navigator && window.isSecureContext && serviceWorkerUrl) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register(serviceWorkerUrl, { scope: '/' }).catch(() => {
            // PWA enhancement must not interfere with ordinary online use.
        });
    }, { once: true });
}

function showInstallButtons(visible) {
    const alreadyInstalled = window.matchMedia('(display-mode: standalone)').matches || navigator.standalone === true;
    installButtons.forEach((button) => { button.hidden = !visible || alreadyInstalled; });
}

showInstallButtons(false);
window.addEventListener('beforeinstallprompt', (event) => {
    event.preventDefault();
    installPrompt = event;
    showInstallButtons(true);
});

installButtons.forEach((button) => button.addEventListener('click', async () => {
    if (!installPrompt) return;

    const prompt = installPrompt;
    installPrompt = null;
    showInstallButtons(false);
    await prompt.prompt();
    await prompt.userChoice;
}));

window.addEventListener('appinstalled', () => {
    installPrompt = null;
    showInstallButtons(false);
});
