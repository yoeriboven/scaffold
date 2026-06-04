<script setup lang="ts">
import { Head, setLayoutProps } from '@inertiajs/vue3';
import AuthLayout from '@/layouts/AuthLayout.vue';

defineOptions({
    layout: AuthLayout,
});

const props = defineProps<{
    code: number;
}>();

const localTitle = {
    503: '503: Service Unavailable',
    500: '500: Server Error',
    404: '404: Page Not Found',
    403: '403: Forbidden',
}[props.code];

const localDescription = {
    503: 'Sorry, we are doing some maintenance. Please check back soon.',
    500: 'Whoops, something went wrong on our servers.',
    404: 'Sorry, the page you are looking for could not be found.',
    403: 'Sorry, you are forbidden from accessing this page.',
}[props.code];

setLayoutProps({
    title: localTitle,
    description: localDescription,
});
</script>

<template>
    <Head :title="localTitle" />
</template>
