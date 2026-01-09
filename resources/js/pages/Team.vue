<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head :title="$t('Team Management')" />

        <div class="flex flex-col gap-12 p-6 lg:p-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">
                        {{ $t('Team') }}
                    </h1>
                    <p class="mt-2 text-sm text-muted-foreground">
                        {{ $t('Manage your team members and invitations') }}
                    </p>
                </div>
                <InviteTeamMemberDialog />
            </div>

            <!-- Team Members Section -->
            <div class="space-y-6">
                <div>
                    <h2 class="text-lg font-medium">{{ $t('Team Members') }}</h2>
                    <p class="mt-1 text-sm text-muted-foreground">
                        {{ team.members.length }}
                        {{
                            team.members.length === 1
                                ? $t('member')
                                : $t('members')
                        }}
                        {{ $t('in your team') }}
                    </p>
                </div>
                <div class="rounded-lg border">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead class="h-12 px-4">{{
                                    $t('Name')
                                }}</TableHead>
                                <TableHead class="h-12 px-4">{{
                                    $t('Email')
                                }}</TableHead>
                                <TableHead class="h-12 px-4">{{
                                    $t('Role')
                                }}</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow
                                v-for="member in team.members"
                                :key="member.id"
                            >
                                <TableCell class="px-4 py-4 font-medium">
                                    {{ member.name }}
                                </TableCell>
                                <TableCell
                                    class="px-4 py-4 text-muted-foreground"
                                >
                                    {{ member.email }}
                                </TableCell>
                                <TableCell class="px-4 py-4">
                                    <span class="text-muted-foreground">
                                        {{ member.role }}
                                    </span>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
            </div>

            <!-- Pending Invitations Section -->
            <div v-if="team.invitations.length > 0" class="space-y-6">
                <div>
                    <h2 class="text-lg font-medium">
                        {{ $t('Pending Invitations') }}
                    </h2>
                    <p class="mt-1 text-sm text-muted-foreground">
                        {{ team.invitations.length }}
                        {{
                            team.invitations.length === 1
                                ? $t('invitation')
                                : $t('invitations')
                        }}
                        {{ $t('waiting for acceptance') }}
                    </p>
                </div>
                <div class="rounded-lg border">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead class="h-12 px-4">{{
                                    $t('Email')
                                }}</TableHead>
                                <TableHead class="h-12 px-4">{{
                                    $t('Role')
                                }}</TableHead>
                                <TableHead class="h-12 w-24 px-4 text-right">
                                    {{ $t('Actions') }}
                                </TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow
                                v-for="invitation in team.invitations"
                                :key="invitation.id"
                            >
                                <TableCell class="px-4 py-4 font-medium">
                                    {{ invitation.email }}
                                </TableCell>
                                <TableCell class="px-4 py-4">
                                    <span class="text-muted-foreground">
                                        {{ invitation.role }}
                                    </span>
                                </TableCell>
                                <TableCell class="px-4 py-4 text-right">
                                    <RevokeInvitationDialog
                                        :invitation="invitation"
                                    />
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';

import InviteTeamMemberDialog from '@/components/InviteTeamMemberDialog.vue';
import RevokeInvitationDialog from '@/components/RevokeInvitationDialog.vue';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

interface Member {
    id: number;
    email: string;
    name: string;
    role: string;
    role_value: string;
}

interface Invitation {
    id: number;
    email: string;
    role: string;
}

interface Props {
    team: {
        members: Member[];
        invitations: Invitation[];
    };
}

defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: trans('Team'),
        href: route('team'),
    },
];
</script>
