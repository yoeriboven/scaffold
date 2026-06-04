<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import ShowTeamController from '@/actions/App/Http/Controllers/Teams/ShowTeamController';
import InviteTeamMemberDialog from '@/components/InviteTeamMemberDialog.vue';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';

type Member = {
    name: string;
    email: string;
    role: string;
};

type Invitation = {
    email: string;
    role: string;
};

defineProps<{
    team: {
        members: Member[];
        invitations: Invitation[];
    };
}>();

defineOptions({
    layout: [
        AppLayout,
        {
            title: 'Team',
            breadcrumbs: [
                {
                    title: 'Team',
                    href: ShowTeamController,
                },
            ],
        },
    ],
});
</script>

<template>
    <Head title="Team" />

    <div class="flex flex-col gap-12 p-6 lg:p-8">
        <!-- Team Members Section -->
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-medium">Team members</h2>
                    <p class="mt-1 text-sm text-muted-foreground">
                        {{ team.members.length }}
                        {{ team.members.length === 1 ? 'member' : 'members' }}
                        in your team
                    </p>
                </div>
                <InviteTeamMemberDialog />
            </div>

            <div class="rounded-lg border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="h-12 px-4">Name</TableHead>
                            <TableHead class="h-12 px-4">Email</TableHead>
                            <TableHead class="h-12 px-4">Role</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="member in team.members" :key="member.email">
                            <TableCell class="px-4 py-4 font-medium">
                                {{ member.name }}
                            </TableCell>
                            <TableCell class="px-4 py-4 text-muted-foreground">
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
                <h2 class="text-lg font-medium">Pending invitations</h2>
                <p class="mt-1 text-sm text-muted-foreground">
                    {{ team.invitations.length }}
                    {{ team.invitations.length === 1 ? 'invitation' : 'invitations' }}
                    waiting for acceptance
                </p>
            </div>
            <div class="rounded-lg border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="h-12 px-4">Email</TableHead>
                            <TableHead class="h-12 px-4">Role</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="invitation in team.invitations" :key="invitation.email">
                            <TableCell class="px-4 py-4 font-medium">
                                {{ invitation.email }}
                            </TableCell>
                            <TableCell class="px-4 py-4">
                                <span class="text-muted-foreground">
                                    {{ invitation.role }}
                                </span>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </div>
</template>
