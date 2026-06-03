import { computed, onMounted } from 'vue';

/**
 * See @/pages/auth/Register.vue for an example implementation
 */

export function initializeTurnstile(selector = '#turnstile-container') {
    const turnstileEnabled = computed(function () {
        const key = import.meta.env.VITE_CLOUDFLARE_TURNSTILE_SITE_KEY;

        return key !== null && key !== '' && key.trim() !== '';
    });

    onMounted(() => {
        if (turnstileEnabled.value) {
            startTurnstile();
        }
    });

    function startTurnstile() {
        const script = document.createElement('script');
        script.src =
            'https://challenges.cloudflare.com/turnstile/v0/api.js?onload=onTurnstileLoad&render=explicit';
        script.async = true;
        document.head.appendChild(script);

        window.onTurnstileLoad = () => {
            window.turnstile.render(selector, {
                sitekey: import.meta.env.VITE_CLOUDFLARE_TURNSTILE_SITE_KEY,

                /**
                 * If you can't create the #turnstile-container inside a <form>
                 * you can reach for the token programmatically
                 *
                 * callback: (token) => {
                 *     form['cf-turnstile-response'] = token;
                 * },
                 */
            });
        };
    }
}
