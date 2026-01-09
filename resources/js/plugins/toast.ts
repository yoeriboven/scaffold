import { router, usePage } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';

export default () => {
    router.on('finish', () => {
        const body = usePage().props.toast;

        if (body && body.message) {
            if (body.level === 'success') {
                toast.success(body.message);
            } else if (body.level === 'error') {
                toast.error(body.message);
            } else {
                toast(body.message);
            }
        }
    });
};
