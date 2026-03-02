<script setup lang="ts">
import { Briefcase, Calendar, MapPin, Search } from 'lucide-vue-next';
import { onMounted, ref, computed } from 'vue';
import api from '@/lib/api';

const jobs = ref<any[]>([]);
const loading = ref(true);
const query = ref('');
const filter = ref('');

onMounted(async () => {
    const { data } = await api.get('/public/jobs');
    jobs.value = data.data;
    loading.value = false;
});

const typeColors: Record<string, string> = {
    full_time: 'bg-blue-50 text-blue-700 ring-1 ring-blue-200',
    part_time: 'bg-amber-50 text-amber-700 ring-1 ring-amber-200',
    contract: 'bg-purple-50 text-purple-700 ring-1 ring-purple-200',
    remote: 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200',
};

const filtered = computed(() => {
    return jobs.value.filter((j) => {
        const matchQ = !query.value || j.title.toLowerCase().includes(query.value.toLowerCase()) || (j.location ?? '').toLowerCase().includes(query.value.toLowerCase());
        const matchF = !filter.value || j.employment_type === filter.value;
        return matchQ && matchF;
    });
});

const jobTypes = [
    { value: '', label: 'All Types' },
    { value: 'full_time', label: 'Full-Time' },
    { value: 'part_time', label: 'Part-Time' },
    { value: 'contract', label: 'Contract' },
    { value: 'remote', label: 'Remote' },
];
</script>

<template>
    <div class="min-h-screen bg-white font-sans antialiased">
        <!-- NAV -->
        <header class="sticky top-0 z-50 border-b border-slate-100 bg-white/95 backdrop-blur">
            <div class="mx-auto flex max-w-5xl items-center justify-between px-6 py-4">
                <a href="/" class="flex items-center gap-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-linear-to-br from-violet-600 to-indigo-600">
                        <Briefcase class="h-4 w-4 text-white" />
                    </div>
                    <span class="text-xl font-bold tracking-tight text-slate-900">Talentry</span>
                </a>
                <nav class="flex items-center gap-4">
                    <a href="/" class="text-sm font-medium text-slate-500 transition hover:text-violet-600">Home</a>
                    <a href="/login" class="text-sm font-medium text-slate-500 transition hover:text-violet-600">Log in</a>
                    <a href="/register" class="rounded-lg bg-violet-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-violet-700">Sign up</a>
                </nav>
            </div>
        </header>

        <!-- HERO STRIP -->
        <section class="bg-linear-to-br from-slate-950 via-violet-950 to-indigo-950 py-14 text-white">
            <div class="mx-auto max-w-5xl px-6 text-center">
                <h1 class="mb-3 text-4xl font-extrabold tracking-tight lg:text-5xl">Explore Open Positions</h1>
                <p class="mb-8 text-slate-300">Find the role that fits your ambition. Updated daily.</p>
                <!-- Search + filter -->
                <div class="mx-auto flex max-w-2xl flex-col gap-3 sm:flex-row">
                    <div class="flex flex-1 items-center gap-3 overflow-hidden rounded-xl bg-white px-4 shadow-lg">
                        <Search class="h-4 w-4 shrink-0 text-slate-400" />
                        <input
                            v-model="query"
                            type="text"
                            placeholder="Search jobs…"
                            class="w-full py-3 text-sm text-slate-800 outline-none placeholder:text-slate-400"
                        />
                    </div>
                    <select
                        v-model="filter"
                        class="rounded-xl bg-white/15 px-4 py-3 text-sm text-white ring-1 ring-white/20 outline-none backdrop-blur transition hover:bg-white/20"
                    >
                        <option v-for="t in jobTypes" :key="t.value" :value="t.value" class="text-slate-900">{{ t.label }}</option>
                    </select>
                </div>
            </div>
        </section>

        <!-- LISTINGS -->
        <main class="mx-auto max-w-5xl px-6 py-12">
            <!-- Loading -->
            <div v-if="loading" class="space-y-4">
                <div v-for="i in 5" :key="i" class="h-28 animate-pulse rounded-2xl bg-slate-100"></div>
            </div>

            <!-- Empty -->
            <div v-else-if="!filtered.length" class="py-24 text-center">
                <Briefcase class="mx-auto mb-3 h-12 w-12 text-slate-200" />
                <p class="text-lg font-medium text-slate-400">No positions match your search.</p>
                <button v-if="query || filter" class="mt-4 text-sm text-violet-600 hover:underline" @click="query = ''; filter = ''">
                    Clear filters
                </button>
            </div>

            <!-- Job cards -->
            <div v-else class="space-y-4">
                <p class="mb-6 text-sm text-slate-500">{{ filtered.length }} position{{ filtered.length !== 1 ? 's' : '' }} found</p>
                <a
                    v-for="job in filtered"
                    :key="job.uuid"
                    :href="`/careers/${job.uuid}`"
                    class="group flex items-center justify-between rounded-2xl border border-slate-100 bg-white p-5 shadow-sm transition hover:border-violet-200 hover:shadow-md"
                >
                    <div class="flex items-center gap-4">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-linear-to-br from-violet-500 to-indigo-600">
                            <Briefcase class="h-5 w-5 text-white" />
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-slate-900 transition group-hover:text-violet-700">{{ job.title }}</h2>
                            <div class="mt-0.5 flex flex-wrap items-center gap-3 text-xs text-slate-500">
                                <span v-if="job.location" class="flex items-center gap-1">
                                    <MapPin class="h-3 w-3" />{{ job.location }}
                                </span>
                                <span v-if="job.deadline" class="flex items-center gap-1">
                                    <Calendar class="h-3 w-3" />Due {{ job.deadline }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <span
                        class="ml-4 hidden shrink-0 rounded-full px-3 py-1 text-xs font-medium sm:inline"
                        :class="typeColors[job.employment_type] ?? 'bg-slate-100 text-slate-600'"
                    >
                        {{ job.employment_type_label }}
                    </span>
                </a>
            </div>
        </main>

        <!-- FOOTER -->
        <footer class="border-t border-slate-100 py-8 text-center text-xs text-slate-400">
            &copy; {{ new Date().getFullYear() }} Talentry &mdash;
            <a href="/" class="hover:text-violet-600">Home</a> &middot;
            <a href="/login" class="hover:text-violet-600">Log in</a>
        </footer>
    </div>
</template>
