<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div
                        class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
                >
                    <div class="flex h-full flex-col items-center justify-center p-6 text-center">
                        <h3 class="text-lg font-medium">Active Jobs</h3>
                        <p class="mt-2 text-3xl font-bold">0</p>
                    </div>
                </div>
                <div
                        class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
                >
                    <div class="flex h-full flex-col items-center justify-center p-6 text-center">
                        <h3 class="text-lg font-medium">Total Applications</h3>
                        <p class="mt-2 text-3xl font-bold">0</p>
                    </div>
                </div>
                <div
                        class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
                >
                    <div class="flex h-full flex-col items-center justify-center p-6 text-center">
                        <h3 class="text-lg font-medium">Candidates</h3>
                        <p class="mt-2 text-3xl font-bold">0</p>
                    </div>
                </div>
            </div>
            <div
                class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border"
            >
                <div class="p-6">
                    <h2 class="text-xl font-semibold mb-4">Recent Activity</h2>
                    <p class="text-muted-foreground">No recent activity to show.</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
