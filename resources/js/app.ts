import toast from '@/plugins/toast';
import { createInertiaApp } from '@inertiajs/vue3';
import { i18nVue } from 'laravel-vue-i18n';
import { ZiggyVue } from 'ziggy-js';
import { initializeTheme } from '@/composables/useAppearance';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

void createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    withApp(app) {
        app.use(toast)
            .use(ZiggyVue)
            .use(i18nVue, {
                lang:
                    window.document.documentElement
                        .getAttribute('lang')
                        ?.replace('-', '_') ?? 'en_US',
                fallbackLang: 'en_US',
                resolve: async (lang: string) => {
                    const langs = import.meta.glob('/lang/*.json');

                    return await langs[`/lang/${lang}.json`]();
                },
            });
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on page load...
initializeTheme();
