<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Form, Head } from '@inertiajs/vue3';
</script>

<template>
    <AuthLayout
        :title="$t('Confirm your password')"
        :description="
            $t(
                'This is a secure area of the application. Please confirm your password before continuing.',
            )
        "
    >
        <Head :title="$t('Confirm password')" />

        <Form
            :action="route('password.confirm.store')"
            method="post"
            reset-on-success
            v-slot="{ errors, processing }"
        >
            <div class="space-y-6">
                <div class="grid gap-2">
                    <Label htmlFor="password">{{ $t('Password') }}</Label>
                    <Input
                        id="password"
                        type="password"
                        name="password"
                        class="mt-1 block w-full"
                        required
                        autocomplete="current-password"
                        autofocus
                    />

                    <InputError :message="errors.password" />
                </div>

                <div class="flex items-center">
                    <Button
                        class="w-full"
                        :disabled="processing"
                        data-test="confirm-password-button"
                    >
                        <Spinner v-if="processing" />
                        {{ $t('Confirm Password') }}
                    </Button>
                </div>
            </div>
        </Form>
    </AuthLayout>
</template>
