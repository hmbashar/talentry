<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { useDashboardStore } from '@/stores/dashboard.store';
import { onMounted } from 'vue';

const dashboardStore = useDashboardStore();
onMounted(() => dashboardStore.fetchStats());
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Dashboard', href: '/dashboard' }]">
        <div class="space-y-8 p-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Overview of your recruitment activity</p>
            </div>

            <!-- Stats Grid -->
            <div v-if="dashboardStore.loading" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div
                    v-for="i in 4"
                    :key="i"
                    class="animate-pulse rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800"
                >
                    <div class="mb-4 h-4 w-24 rounded bg-gray-200 dark:bg-gray-700"></div>
                    <div class="h-8 w-16 rounded bg-gray-200 dark:bg-gray-700"></div>
                </div>
            </div>

            <div v-else-if="dashboardStore.stats" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Jobs</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ dashboardStore.stats.total_jobs }}</p>
                    <p class="mt-1 text-xs text-green-600 dark:text-green-400">{{ dashboardStore.stats.published_jobs }} published</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Draft Jobs</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ dashboardStore.stats.draft_jobs }}</p>
                    <p class="mt-1 text-xs text-yellow-600 dark:text-yellow-400">awaiting publish</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Applications</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ dashboardStore.stats.total_applications }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Hired</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">
                        {{ dashboardStore.stats.applications_by_status?.hired ?? 0 }}
                    </p>
                    <p class="mt-1 text-xs text-green-600 dark:text-green-400">successful placements</p>
                </div>
            </div>

            <!-- Application Status Breakdown -->
            <div
                v-if="dashboardStore.stats?.applications_by_status"
                class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800"
            >
                <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Applications by Status</h2>
                <div class="flex flex-wrap gap-4">
                    <div v-for="(count, status) in dashboardStore.stats.applications_by_status" :key="status" class="flex items-center gap-2">
                        <span
                            class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium"
                            :class="{
                                'bg-blue-100 text-blue-800': status === 'applied',
                                'bg-yellow-100 text-yellow-800': status === 'shortlisted',
                                'bg-purple-100 text-purple-800': status === 'interview',
                                'bg-red-100 text-red-800': status === 'rejected',
                                'bg-green-100 text-green-800': status === 'hired',
                            }"
                        >
                            {{ status }} ({{ count }})
                        </span>
                    </div>
                </div>
            </div>

            <!-- Top Jobs -->
            <div
                v-if="dashboardStore.stats?.top_jobs?.length"
                class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800"
            >
                <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Top Jobs by Applications</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-700">
                                <th class="pb-3 text-left font-medium text-gray-500 dark:text-gray-400">Job Title</th>
                                <th class="pb-3 text-right font-medium text-gray-500 dark:text-gray-400">Applications</th>
                                <th class="pb-3 text-right font-medium text-gray-500 dark:text-gray-400">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                            <tr v-for="job in dashboardStore.stats.top_jobs" :key="job.uuid" class="py-3">
                                <td class="py-3 font-medium text-gray-900 dark:text-white">{{ job.title }}</td>
                                <td class="py-3 text-right text-gray-600 dark:text-gray-300">{{ job.applications_count }}</td>
                                <td class="py-3 text-right">
                                    <span
                                        class="rounded-full px-2 py-0.5 text-xs"
                                        :class="job.status === 'published' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'"
                                    >
                                        {{ job.status }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
