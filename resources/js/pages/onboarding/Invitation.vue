<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import AcceptInvitationController from '@/actions/App/Http/Controllers/Teams/AcceptInvitationController';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/AuthLayout.vue';

const props = defineProps<{
    invitationId: string;
    teamName: string;
}>();

defineOptions({
    layout: [
        AuthLayout,
        {
            title: 'You have been invited',
            description: 'Accept this invitation to join the team',
        },
    ],
});
</script>

<template>
    <Head title="Accept invitation" />

    <Form
        v-bind="AcceptInvitationController.store.form(props.invitationId)"
        v-slot="{ processing }"
        class="flex flex-col gap-6"
    >
        <p class="text-center text-sm text-muted-foreground">
            You have been invited to join
            <span class="font-medium text-foreground">{{ props.teamName }}</span>.
        </p>

        <Button type="submit" class="w-full" :disabled="processing">
            <Spinner v-if="processing" />
            Accept
        </Button>
    </Form>
</template>
