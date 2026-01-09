<script setup lang="ts">
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Form, Head } from '@inertiajs/vue3';

defineProps<{
    status?: string;
}>();
</script>

<template>
    <AuthLayout
        :title="$t('Verify email')"
        :description="
            $t(
                'Please verify your email address by clicking on the link we just emailed to you.',
            )
        "
    >
        <Head :title="$t('Email verification')" />

        <div
            v-if="status === 'verification-link-sent'"
            class="mb-4 text-center text-sm font-medium text-green-600"
        >
            {{
                $t(
                    'A new verification link has been sent to the email address you provided during registration.',
                )
            }}
        </div>

        <Form
            :action="route('verification.send')"
            method="post"
            class="space-y-6 text-center"
            v-slot="{ processing }"
        >
            <Button :disabled="processing" variant="secondary">
                <Spinner v-if="processing" />
                {{ $t('Resend verification email') }}
            </Button>

            <TextLink
                :href="route('logout')"
                as="button"
                class="mx-auto block text-sm"
            >
                {{ $t('Log out') }}
            </TextLink>
        </Form>
    </AuthLayout>
</template>
