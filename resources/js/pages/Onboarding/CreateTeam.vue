<template>
    <AuthBase
        :title="$t('Create Team')"
        :description="$t('Enter your organization name to get started.')"
    >
        <Head :title="$t('Create Team')" />

        <div class="flex flex-col gap-6">
            <Alert class="border-blue-200 bg-blue-50 text-blue-900 dark:border-blue-800 dark:bg-blue-950 dark:text-blue-100">
                <Info class="text-blue-600 dark:text-blue-400" />
                <AlertTitle>{{ $t('Joining an existing team?') }}</AlertTitle>
                <AlertDescription class="text-blue-800 dark:text-blue-200">
                    {{ $t('Close this page and click on the invitation link in your email to join your team.') }}
                </AlertDescription>
            </Alert>

            <Form
                :action="route('onboarding.team.store')"
                method="post"
                v-slot="{ errors, processing }"
                class="flex flex-col gap-6"
            >
                <div class="grid gap-2">
                    <Label for="name">{{ $t('Organization Name') }}</Label>
                    <Input
                        id="name"
                        type="text"
                        name="name"
                        required
                        autofocus
                        autocomplete="off"
                        data-1p-ignore
                        :placeholder="$t('e.g., Acme Inc.')"
                    />
                    <InputError :message="errors.name" />
                </div>

                <Button type="submit" class="w-full" :disabled="processing">
                    <Spinner v-if="processing" />
                    {{ $t('Create Team') }}
                </Button>
            </Form>
        </div>
    </AuthBase>
</template>

<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Form, Head } from '@inertiajs/vue3';
import { Info } from 'lucide-vue-next';
</script>
