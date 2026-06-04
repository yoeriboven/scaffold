import { i18nVue as i18nVuePackage } from 'laravel-vue-i18n';
import type { App, Plugin } from 'vue';

const defaultLanguage = 'en';

export const i18nVue: Plugin = {
    install(app: App, ssr: boolean) {
        if (ssr) {
            app.use(i18nVuePackage, {
                lang: defaultLanguage,
                resolve: (lang: string) => {
                    const langs = import.meta.glob('../../../lang/*.json', { eager: true });

                    return langs[`../../../lang/${lang}.json`].default;
                },
            });

            return
        }

        app.use(i18nVuePackage, {
            resolve: async (lang: string) => {
                const langs = import.meta.glob('../../../lang/*.json');

                return await langs[`../../../lang/${lang}.json`]();
            },
        });
    },
};
