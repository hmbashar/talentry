<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { Briefcase, FileText, LayoutGrid, Settings, ShieldCheck, Users } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import admin from '@/routes/admin';
import type { NavItem, User } from '@/types';

const page = usePage();
const user = computed(() => page.props.auth.user as User);
const isAdmin = computed(() => user.value?.role === 'admin');

const mainNavItems = computed<NavItem[]>(() => [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Jobs',
        href: admin.jobs.index(),
        icon: Briefcase,
    },
    {
        title: 'Applications',
        href: admin.applications.index(),
        icon: FileText,
    },
    {
        title: 'Candidates',
        href: admin.candidates.index(),
        icon: Users,
    },
    ...(isAdmin.value
        ? [
            {
                title: 'Users',
                href: '/admin/users',
                icon: ShieldCheck,
            },
            {
                title: 'Settings',
                href: '/admin/settings/homepage',
                icon: Settings,
            },
          ]
        : []),
]);

const footerNavItems: NavItem[] = [];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
