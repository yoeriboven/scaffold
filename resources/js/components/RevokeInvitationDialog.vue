<template>
    <Dialog>
        <DialogTrigger as-child>
            <Button variant="ghost" size="sm"> {{ $t('Revoke') }} </Button>
        </DialogTrigger>
        <DialogContent>
            <Form
                :action="
                    route('team.invitation.revoke', {
                        invitation: invitation.id,
                    })
                "
                method="delete"
                reset-on-success
                class="space-y-6"
                v-slot="{ processing }"
                :options="{
                    preserveScroll: true,
                }"
            >
                <DialogHeader class="space-y-3">
                    <DialogTitle>{{ $t('Revoke invitation?') }}</DialogTitle>
                    <DialogDescription>
                        {{
                            $t(
                                'Are you sure you want to revoke the invitation for :email? They will no longer be able to join using this invitation.',
                                { email: invitation.email },
                            )
                        }}
                    </DialogDescription>
                </DialogHeader>

                <DialogFooter class="gap-2">
                    <DialogClose as-child>
                        <Button variant="secondary"> {{ $t('Cancel') }} </Button>
                    </DialogClose>

                    <Button
                        type="submit"
                        variant="destructive"
                        :disabled="processing"
                    >
                        {{ $t('Revoke Invitation') }}
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
import { Form } from '@inertiajs/vue3';

import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';

interface Invitation {
    id: number;
    email: string;
    role: string;
}

interface Props {
    invitation: Invitation;
}

defineProps<Props>();
</script>
