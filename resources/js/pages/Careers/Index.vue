<script setup lang="ts">
import { onMounted, ref } from 'vue';
import api from '@/lib/api';

const jobs = ref<any[]>([]);
const loading = ref(true);

onMounted(async () => {
    const { data } = await api.get('/public/jobs');
    jobs.value = data.data;
    loading.value = false;
});

const typeColors: Record<string, string> = {
    full_time: 'bg-blue-100 text-blue-700',
    part_time: 'bg-orange-100 text-orange-700',
    contract: 'bg-purple-100 text-purple-700',
    remote: 'bg-green-100 text-green-700',
};
</script>

<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-950">
        <!-- Header -->
        <header class="border-b border-gray-100 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="mx-auto max-w-5xl px-6 py-5">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">🚀 Talentry Careers</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Join our team — explore open positions below</p>
            </div>
        </header>

        <main class="mx-auto max-w-5xl px-6 py-10">
            <!-- Loading -->
            <div v-if="loading" class="space-y-4">
                <div v-for="i in 4" :key="i" class="animate-pulse rounded-xl bg-white p-6 shadow-sm dark:bg-gray-800">
                    <div class="mb-3 h-5 w-48 rounded bg-gray-200 dark:bg-gray-700"></div>
                    <div class="h-4 w-32 rounded bg-gray-100 dark:bg-gray-700"></div>
                </div>
            </div>

            <!-- Job Listings -->
            <div v-else class="space-y-4">
                <a
                    v-for="job in jobs"
                    :key="job.uuid"
                    :href="`/careers/${job.uuid}`"
                    class="block rounded-xl border border-gray-100 bg-white p-6 shadow-sm transition hover:border-blue-200 hover:shadow-md dark:border-gray-700 dark:bg-gray-800 dark:hover:border-blue-700"
                >
                    <div class="flex items-start justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ job.title }}</h2>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">📍 {{ job.location }}</p>
                        </div>
                        <span
                            class="rounded-full px-3 py-1 text-xs font-medium"
                            :class="typeColors[job.employment_type] ?? 'bg-gray-100 text-gray-600'"
                        >
                            {{ job.employment_type_label }}
                        </span>
                    </div>
                    <div v-if="job.deadline" class="mt-3 text-xs text-gray-400">
                        Apply before: {{ job.deadline }}
                    </div>
                </a>

                <div v-if="!jobs.length" class="py-24 text-center">
                    <p class="text-lg font-medium text-gray-400">No open positions right now.</p>
                    <p class="mt-1 text-sm text-gray-400">Check back soon!</p>
                </div>
            </div>
        </main>
    </div>
</template>
