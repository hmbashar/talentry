<script setup lang="ts">
import { onMounted, ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { useApplicationStore } from '@/stores/application.store';

const applicationStore = useApplicationStore();
const statusFilter = ref('');

onMounted(() => applicationStore.fetchApplications());

function applyFilter() {
    applicationStore.fetchApplications(statusFilter.value ? { status: statusFilter.value } : {});
}

const statusColors: Record<string, string> = {
    applied: 'bg-blue-100 text-blue-700',
    shortlisted: 'bg-yellow-100 text-yellow-700',
    interview: 'bg-purple-100 text-purple-700',
    rejected: 'bg-red-100 text-red-700',
    hired: 'bg-green-100 text-green-700',
};

const statuses = ['', 'applied', 'shortlisted', 'interview', 'rejected', 'hired'];
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Applications', href: '/admin/applications' }]">
        <div class="space-y-6 p-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Applications</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Review and manage all candidate applications</p>
            </div>

            <!-- Status Filter -->
            <div class="flex gap-2 flex-wrap">
                <button
                    v-for="s in statuses"
                    :key="s"
                    class="rounded-full px-3 py-1.5 text-xs font-medium transition"
                    :class="statusFilter === s
                        ? 'bg-blue-600 text-white'
                        : 'border border-gray-200 text-gray-600 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300'"
                    @click="statusFilter = s; applyFilter()"
                >
                    {{ s ? s.charAt(0).toUpperCase() + s.slice(1) : 'All' }}
                </button>
            </div>

            <!-- Loading -->
            <div v-if="applicationStore.loading" class="space-y-3">
                <div
                    v-for="i in 6"
                    :key="i"
                    class="animate-pulse rounded-xl border border-gray-100 bg-white p-5 dark:border-gray-700 dark:bg-gray-800"
                >
                    <div class="mb-2 h-4 w-48 rounded bg-gray-200 dark:bg-gray-700"></div>
                    <div class="h-3 w-32 rounded bg-gray-100 dark:bg-gray-700"></div>
                </div>
            </div>

            <!-- Applications Table -->
            <div v-else class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <table class="w-full text-sm">
                    <thead class="border-b border-gray-100 bg-gray-50 dark:border-gray-700 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-5 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Candidate</th>
                            <th class="px-5 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Job</th>
                            <th class="px-5 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Status</th>
                            <th class="px-5 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Applied</th>
                            <th class="px-5 py-3 text-right font-medium text-gray-500 dark:text-gray-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                        <tr
                            v-for="app in applicationStore.applications"
                            :key="app.uuid"
                            class="transition hover:bg-gray-50 dark:hover:bg-gray-700/30"
                        >
                            <td class="px-5 py-4">
                                <p class="font-medium text-gray-900 dark:text-white">{{ app.candidate?.name }}</p>
                                <p class="text-xs text-gray-500">{{ app.candidate?.email }}</p>
                            </td>
                            <td class="px-5 py-4 text-gray-700 dark:text-gray-300">{{ app.job_posting?.title }}</td>
                            <td class="px-5 py-4">
                                <span class="rounded-full px-2.5 py-1 text-xs font-medium" :class="statusColors[app.status] ?? 'bg-gray-100 text-gray-600'">
                                    {{ app.status_label }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-gray-500 dark:text-gray-400">{{ new Date(app.created_at).toLocaleDateString() }}</td>
                            <td class="px-5 py-4 text-right">
                                <a
                                    :href="`/admin/applications/${app.uuid}`"
                                    class="rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300"
                                >
                                    Review
                                </a>
                            </td>
                        </tr>
                        <tr v-if="!applicationStore.applications.length">
                            <td colspan="5" class="py-12 text-center text-gray-400">No applications found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
