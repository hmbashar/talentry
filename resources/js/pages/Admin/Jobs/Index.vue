<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { useJobStore } from '@/stores/job.store';

const jobStore = useJobStore();
const statusFilter = ref('');

onMounted(() => jobStore.fetchJobs());

function applyFilter() {
    jobStore.fetchJobs(statusFilter.value ? { status: statusFilter.value } : {});
}

async function toggleStatus(job: any) {
    if (job.status === 'published') {
        await jobStore.draftJob(job.uuid);
    } else {
        await jobStore.publishJob(job.uuid);
    }
}

async function deleteJob(uuid: string) {
    if (confirm('Are you sure you want to delete this job?')) {
        await jobStore.deleteJob(uuid);
    }
}
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Jobs', href: '/admin/jobs' }]">
        <div class="space-y-6 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Job Postings</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage all open and draft positions</p>
                </div>
                <Link
                    href="/admin/jobs/create"
                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700"
                >
                    + New Job
                </Link>
            </div>

            <!-- Filters -->
            <div class="flex gap-3">
                <select
                    v-model="statusFilter"
                    class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                    @change="applyFilter"
                >
                    <option value="">All Statuses</option>
                    <option value="published">Published</option>
                    <option value="draft">Draft</option>
                </select>
            </div>

            <!-- Loading -->
            <div v-if="jobStore.loading" class="space-y-3">
                <div
                    v-for="i in 5"
                    :key="i"
                    class="animate-pulse rounded-xl border border-gray-100 bg-white p-5 dark:border-gray-700 dark:bg-gray-800"
                >
                    <div class="mb-2 h-4 w-48 rounded bg-gray-200 dark:bg-gray-700"></div>
                    <div class="h-3 w-32 rounded bg-gray-100 dark:bg-gray-700"></div>
                </div>
            </div>

            <!-- Job Cards -->
            <div v-else class="space-y-3">
                <div
                    v-for="job in jobStore.jobs"
                    :key="job.uuid"
                    class="flex items-center justify-between rounded-xl border border-gray-100 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800"
                >
                    <div class="flex-1">
                        <div class="flex items-center gap-3">
                            <h3 class="font-semibold text-gray-900 dark:text-white">{{ job.title }}</h3>
                            <span
                                class="rounded-full px-2 py-0.5 text-xs font-medium"
                                :class="job.status === 'published' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'"
                            >
                                {{ job.status_label }}
                            </span>
                        </div>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            {{ job.location }} · {{ job.employment_type_label }}
                            <span v-if="job.deadline"> · Deadline: {{ job.deadline }}</span>
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button
                            class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-medium transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700"
                            @click="toggleStatus(job)"
                        >
                            {{ job.status === 'published' ? 'Unpublish' : 'Publish' }}
                        </button>
                        <Link
                            :href="`/admin/jobs/${job.uuid}/edit`"
                            class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-medium transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300"
                        >
                            Edit
                        </Link>
                        <button
                            class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-medium text-red-600 transition hover:bg-red-50"
                            @click="deleteJob(job.uuid)"
                        >
                            Delete
                        </button>
                    </div>
                </div>

                <div v-if="!jobStore.jobs.length" class="py-16 text-center text-gray-400">
                    <p class="text-lg font-medium">No jobs found</p>
                    <p class="mt-1 text-sm">Create your first job posting to get started.</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
