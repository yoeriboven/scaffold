import { i18nVue as i18nVuePackage } from 'laravel-vue-i18n';
import type { App, Plugin } from 'vue';

const defaultLanguage = 'en';

// Resolved once, statically, by Vite at build time. Sync resolution works for
// both the client and the SSR build, and keeps lang files in a single chunk.
const langs = import.meta.glob<{ default: Record<string, string> }>('../../../lang/*.json', { eager: true });

export const i18nVue: Plugin = {
    install(app: App, ssr: boolean) {
        app.use(i18nVuePackage, {
            lang: ssr ? defaultLanguage : undefined,
            resolve: (lang: string) => langs[`../../../lang/${lang}.json`]?.default,
        });
    },
};
