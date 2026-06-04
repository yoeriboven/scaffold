<script setup lang="ts">
// import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { SidebarTrigger } from '@/components/ui/sidebar';
import type { BreadcrumbItem } from '@/types';

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItem[];
        title?: string;
    }>(),
    {
        breadcrumbs: () => [],
    },
);
</script>

<template>
    <header class="flex h-16 shrink-0 items-center gap-2 border-b border-sidebar-border/70 px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 lg:px-8">
        <div class="flex w-full items-center justify-between gap-2">
            <!--            <template v-if="breadcrumbs && breadcrumbs.length > 0">-->
            <!--                <Breadcrumbs :breadcrumbs="breadcrumbs" />-->
            <!--            </template>-->
            <h1 class="truncate text-lg font-semibold tracking-tight text-foreground">{{ title }}</h1>
            <SidebarTrigger />
        </div>
    </header>
</template>
