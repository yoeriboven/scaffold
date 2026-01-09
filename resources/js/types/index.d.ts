import { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon;
    isActive?: boolean;
}

export type AppPageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: Auth;
    sidebarOpen: boolean;
    toast: { level: 'success' | 'error'; message: string };
};

export interface User {
    id: number;
    name: string;
    email: string;
    permissions: Permissions;
}

export interface Permissions {
    manage_team: boolean;
}

export type BreadcrumbItemType = BreadcrumbItem;
