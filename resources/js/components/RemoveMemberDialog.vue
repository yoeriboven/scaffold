<template>
    <Dialog>
        <DialogTrigger as-child>
            <Button variant="ghost" size="sm"> Remove </Button>
        </DialogTrigger>
        <DialogContent>
            <Form
                :action="route('team.member.remove', { user: member.id })"
                method="delete"
                reset-on-success
                class="space-y-6"
                v-slot="{ processing }"
            >
                <DialogHeader class="space-y-3">
                    <DialogTitle>Remove team member?</DialogTitle>
                    <DialogDescription>
                        Are you sure you want to remove {{ member.name }} from
                        the team? They will lose access immediately.
                    </DialogDescription>
                </DialogHeader>

                <DialogFooter class="gap-2">
                    <DialogClose as-child>
                        <Button variant="secondary"> Cancel </Button>
                    </DialogClose>

                    <Button
                        type="submit"
                        variant="destructive"
                        :disabled="processing"
                    >
                        Remove Member
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

interface Member {
    id: number;
    email: string;
    name: string;
    role: string;
}

interface Props {
    member: Member;
}

defineProps<Props>();
</script>
