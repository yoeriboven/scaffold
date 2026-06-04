<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { UserPlus } from '@lucide/vue';
import { ref } from 'vue';
import InviteUserController from '@/actions/App/Http/Controllers/Teams/InviteUserController';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const open = ref(false);
</script>

<template>
    <Dialog v-model:open="open">
        <DialogTrigger as-child>
            <Button>
                <UserPlus class="size-4" />
                Invite member
            </Button>
        </DialogTrigger>
        <DialogContent>
            <Form v-bind="InviteUserController.form()" reset-on-success class="space-y-6" v-slot="{ errors, processing }" @success="open = false">
                <DialogHeader class="space-y-3">
                    <DialogTitle>Invite team member</DialogTitle>
                    <DialogDescription> Send an invitation to join your team. They will receive an email with instructions to accept. </DialogDescription>
                </DialogHeader>

                <div class="space-y-2">
                    <Label for="email">Email address</Label>
                    <Input id="email" type="email" name="email" placeholder="colleague@example.com" required autocomplete="off" data-1p-ignore />
                    <InputError :message="errors.email" />
                </div>

                <DialogFooter class="gap-2">
                    <DialogClose as-child>
                        <Button type="button" variant="secondary"> Cancel </Button>
                    </DialogClose>

                    <Button type="submit" :disabled="processing"> Send invitation </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
