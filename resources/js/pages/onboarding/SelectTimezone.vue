<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import StoreTimezoneController from '@/actions/App/Http/Controllers/Onboarding/StoreTimezoneController';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/AuthLayout.vue';

const props = defineProps<{
    timezones: string[];
}>();

defineOptions({
    layout: [
        AuthLayout,
        {
            title: 'Select your timezone',
            description:
                'We use this to show dates and times in your local time',
        },
    ],
});

const detectedTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
const defaultTimezone = props.timezones.includes(detectedTimezone)
    ? detectedTimezone
    : undefined;
</script>

<template>
    <Head title="Select timezone" />

    <Form
        v-bind="StoreTimezoneController.form()"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-6"
    >
        <div class="grid gap-2">
            <Label for="timezone">Timezone</Label>
            <Select name="timezone" :default-value="defaultTimezone" autofocus>
                <SelectTrigger id="timezone" class="w-full" :tabindex="1">
                    <SelectValue placeholder="Select a timezone" />
                </SelectTrigger>
                <SelectContent side="bottom" :avoid-collisions="false">
                    <SelectItem v-for="tz in timezones" :key="tz" :value="tz">
                        {{ tz }}
                    </SelectItem>
                </SelectContent>
            </Select>
            <InputError :message="errors.timezone" />
        </div>

        <Button
            type="submit"
            class="mt-2 w-full"
            :tabindex="2"
            :disabled="processing"
        >
            <Spinner v-if="processing" />
            Continue
        </Button>
    </Form>
</template>
