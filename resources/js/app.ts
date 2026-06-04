import { createInertiaApp } from '@inertiajs/vue3';
import { initializeFlashToast } from '@/lib/flashToast';
import { i18nVue } from '@/lib/i18n';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

void createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    progress: {
        color: '#4B5563',
    },
    withApp(app, { ssr }) {
        app.use(i18nVue, ssr);
    },
});

// This will listen for flash toast data from the server...
initializeFlashToast();
