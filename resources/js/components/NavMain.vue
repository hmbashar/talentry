<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import type { NavItem } from '@/types';

defineProps<{
    items: NavItem[];
}>();

const { isCurrentUrl } = useCurrentUrl();
</script>

<template>
    <SidebarGroup class="px-3 py-2">
        <SidebarGroupLabel class="text-[10px] font-semibold uppercase tracking-widest text-sidebar-foreground/40 mb-1 px-2">
            Navigation
        </SidebarGroupLabel>
        <SidebarMenu class="space-y-0.5">
            <SidebarMenuItem v-for="item in items" :key="item.title">
                <SidebarMenuButton
                    as-child
                    :is-active="isCurrentUrl(item.href)"
                    :tooltip="item.title"
                    class="group h-9 rounded-lg px-3 transition-all duration-150
                        data-[active=true]:bg-violet-600 data-[active=true]:text-white data-[active=true]:shadow-sm
                        hover:bg-sidebar-accent/60"
                >
                    <Link :href="item.href" class="flex items-center gap-3">
                        <component :is="item.icon" class="h-4 w-4 shrink-0 transition-transform group-hover:scale-110" />
                        <span class="text-sm font-medium">{{ item.title }}</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
