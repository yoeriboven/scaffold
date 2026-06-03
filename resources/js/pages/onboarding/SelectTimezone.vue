<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import StoreTimezoneController from '@/actions/App/Http/Controllers/Onboarding/StoreTimezoneController';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/AuthLayout.vue';

defineOptions({ layout: AuthLayout });

const props = defineProps<{
    timezones: string[];
}>();

const detectedTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
const defaultTimezone = props.timezones.includes(detectedTimezone)
    ? detectedTimezone
    : undefined;
</script>

<template>
    <Form
        v-bind="StoreTimezoneController.form()"
        v-slot="{ errors, processing }"
        class="space-y-6"
    >
        <div>
            <select name="timezone">
                <option
                    v-for="tz in timezones"
                    :key="tz"
                    :value="tz"
                    :selected="tz === defaultTimezone"
                >
                    {{ tz }}
                </option>
            </select>
            <InputError :message="errors.timezone" />
        </div>

        <Button
            type="submit"
            class="mt-2 w-full"
            tabindex="5"
            :disabled="processing"
            data-test="register-user-button"
        >
            <Spinner v-if="processing" />
            Create account
        </Button>
    </Form>
</template>
