<template>
    <Dialog v-model:open="open">
        <DialogTrigger as-child>
            <Button>
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="mr-2 size-4"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <line x1="19" x2="19" y1="8" y2="14" />
                    <line x1="22" x2="16" y1="11" y2="11" />
                </svg>
                {{ $t('Invite Member') }}
            </Button>
        </DialogTrigger>
        <DialogContent>
            <Form
                :action="route('team.invite')"
                method="post"
                reset-on-success
                class="space-y-6"
                v-slot="{ errors, processing }"
                @success="open = false"
            >
                <DialogHeader class="space-y-3">
                    <DialogTitle>{{ $t('Invite Team Member') }}</DialogTitle>
                    <DialogDescription>
                        {{
                            $t(
                                'Send an invitation to join your team. They will receive an email with instructions to accept.',
                            )
                        }}
                    </DialogDescription>
                </DialogHeader>

                <div class="space-y-2">
                    <Label for="email">{{ $t('Email address') }}</Label>
                    <Input
                        id="email"
                        type="email"
                        name="email"
                        placeholder="colleague@example.com"
                        required
                        autocomplete="off"
                        data-1p-ignore
                    />
                    <InputError :message="errors.email" />
                </div>

                <DialogFooter class="gap-2">
                    <DialogClose as-child>
                        <Button type="button" variant="secondary">
                            {{ $t('Cancel') }}
                        </Button>
                    </DialogClose>

                    <Button type="submit" :disabled="processing">
                        {{ $t('Send Invitation') }}
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { ref } from 'vue';

import InputError from '@/components/InputError.vue';
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
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const open = ref(false);
</script>
