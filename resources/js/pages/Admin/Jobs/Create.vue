<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { useJobStore } from '@/stores/job.store';

const jobStore = useJobStore();

const form = ref({
    title: '',
    description: '',
    location: '',
    employment_type: 'full_time',
    deadline: '',
});
const error = ref<string | null>(null);
const submitting = ref(false);

const employmentTypes = [
    { value: 'full_time', label: 'Full-Time' },
    { value: 'part_time', label: 'Part-Time' },
    { value: 'contract', label: 'Contract' },
    { value: 'remote', label: 'Remote' },
];

async function submit() {
    submitting.value = true;
    error.value = null;
    try {
        await jobStore.createJob(form.value);
        router.visit('/admin/jobs');
    } catch (e: any) {
        error.value = e.response?.data?.message ?? 'Failed to create job.';
    } finally {
        submitting.value = false;
    }
}
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Jobs', href: '/admin/jobs' }, { title: 'Create', href: '/admin/jobs/create' }]">
        <div class="mx-auto max-w-2xl space-y-6 p-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create Job Posting</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Fill in the details below to create a new job posting</p>
            </div>

            <form class="space-y-5" @submit.prevent="submit">
                <div v-if="error" class="rounded-lg bg-red-50 p-3 text-sm text-red-600 dark:bg-red-900/30 dark:text-red-400">
                    {{ error }}
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Job Title</label>
                    <input
                        v-model="form.title"
                        type="text"
                        required
                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                    />
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                    <textarea
                        v-model="form.description"
                        rows="8"
                        required
                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                    ></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Location</label>
                        <input
                            v-model="form.location"
                            type="text"
                            required
                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                        />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Employment Type</label>
                        <select
                            v-model="form.employment_type"
                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                        >
                            <option v-for="t in employmentTypes" :key="t.value" :value="t.value">{{ t.label }}</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Application Deadline (optional)</label>
                    <input
                        v-model="form.deadline"
                        type="date"
                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                    />
                </div>

                <div class="flex justify-end gap-3">
                    <a
                        href="/admin/jobs"
                        class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300"
                    >
                        Cancel
                    </a>
                    <button
                        type="submit"
                        :disabled="submitting"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-50"
                    >
                        {{ submitting ? 'Creating...' : 'Create Job' }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
