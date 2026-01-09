<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Form, Head } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';

const props = defineProps<{
    invitationId: string;
    teamName: string;
}>();

const pageTitle = trans('Join :team', { team: props.teamName });
const pageDescription = trans("You've been invited to join :team", {
    team: props.teamName,
});
</script>

<template>
    <AuthBase :title="$t(`You've been invited`)" :description="pageDescription">
        <Head :title="pageTitle" />

        <Form
            :action="route('invitation.accept.store', invitationId)"
            method="post"
            v-slot="{ processing }"
            class="flex flex-col gap-6"
        >
            <p class="text-center text-sm text-muted-foreground">
                {{
                    $t(
                        'Click the button below to accept the invitation and join the team.',
                    )
                }}
            </p>

            <Button type="submit" class="w-full" :disabled="processing">
                <Spinner v-if="processing" />
                {{ $t('Join :team', { team: teamName }) }}
            </Button>
        </Form>
    </AuthBase>
</template>
