<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Briefcase, Calendar, Clock, Globe, MapPin, Pencil, Plus, Search, Trash2, Users } from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { useJobStore } from '@/stores/job.store';

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Jobs', href: '/admin/jobs' },
];

const jobStore = useJobStore();
const search = ref('');
const activeFilter = ref<'all' | 'published' | 'draft'>('all');

onMounted(() => jobStore.fetchJobs());

function applyFilter(filter: 'all' | 'published' | 'draft') {
    activeFilter.value = filter;
    const params: Record<string, string> = {};
    if (search.value) params.search = search.value;
    if (filter !== 'all') params.status = filter;
    jobStore.fetchJobs(params);
}

function applySearch() {
    const params: Record<string, string> = {};
    if (search.value) params.search = search.value;
    if (activeFilter.value !== 'all') params.status = activeFilter.value;
    jobStore.fetchJobs(params);
}

async function toggleStatus(job: any) {
    if (job.status === 'published') {
        await jobStore.draftJob(job.uuid);
    } else {
        await jobStore.publishJob(job.uuid);
    }
}

async function deleteJob(uuid: string) {
    if (confirm('Are you sure you want to delete this job posting?')) {
        await jobStore.deleteJob(uuid);
    }
}

const totalJobs = computed(() => jobStore.meta?.total ?? jobStore.jobs.length);
const publishedCount = computed(() => jobStore.jobs.filter((j) => j.status === 'published').length);
const draftCount = computed(() => jobStore.jobs.filter((j) => j.status === 'draft').length);
const withApplications = computed(() => jobStore.jobs.filter((j) => (j.applications_count ?? 0) > 0).length);

const statusStyles: Record<string, string> = {
    published: 'bg-emerald-100 text-emerald-700 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-300',
    draft: 'bg-slate-100 text-slate-600 border-slate-200 dark:bg-slate-800 dark:text-slate-400',
};

const employmentTypeColors: Record<string, string> = {
    'full-time': 'bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300',
    'part-time': 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
    contract: 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
    freelance: 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300',
    internship: 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300',
};

const jobIconColors = [
    'from-violet-500 to-indigo-600',
    'from-indigo-500 to-blue-600',
    'from-blue-500 to-cyan-600',
    'from-amber-500 to-orange-600',
    'from-emerald-500 to-teal-600',
    'from-rose-500 to-pink-600',
];

function getJobIconGradient(title: string): string {
    let hash = 0;
    for (let i = 0; i < title.length; i++) hash = title.charCodeAt(i) + ((hash << 5) - hash);
    return jobIconColors[Math.abs(hash) % jobIconColors.length];
}

function formatDeadline(date: string): string {
    return new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}

function isExpiringSoon(date: string): boolean {
    const diff = new Date(date).getTime() - Date.now();
    return diff > 0 && diff < 7 * 24 * 60 * 60 * 1000;
}

function isPast(date: string): boolean {
    return new Date(date).getTime() < Date.now();
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex-1 space-y-6 p-6">
            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Job Postings</h1>
                    <p class="mt-1 text-sm text-muted-foreground">Manage your open positions and track applicants</p>
                </div>
                <Button class="gap-2 bg-violet-600 hover:bg-violet-700 text-white shadow-sm" as-child>
                    <Link href="/admin/jobs/create">
                        <Plus class="h-4 w-4" />
                        Post a Job
                    </Link>
                </Button>
            </div>

            <!-- Stats / Filter Tabs -->
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                <button
                    class="group relative overflow-hidden rounded-xl border p-4 text-left transition-all hover:shadow-md"
                    :class="activeFilter === 'all' ? 'border-violet-200 bg-violet-50 dark:border-violet-800 dark:bg-violet-900/20' : 'border-border bg-card hover:border-violet-200'"
                    @click="applyFilter('all')"
                >
                    <p class="text-xs font-medium text-muted-foreground">Total Jobs</p>
                    <p class="mt-1 text-2xl font-bold" :class="activeFilter === 'all' ? 'text-violet-700 dark:text-violet-300' : ''">
                        {{ jobStore.loading ? '—' : totalJobs }}
                    </p>
                    <div
                        class="absolute right-3 top-3 flex h-7 w-7 items-center justify-center rounded-lg"
                        :class="activeFilter === 'all' ? 'bg-violet-100 dark:bg-violet-900/40' : 'bg-muted'"
                    >
                        <Briefcase class="h-3.5 w-3.5" :class="activeFilter === 'all' ? 'text-violet-600' : 'text-muted-foreground'" />
                    </div>
                </button>

                <button
                    class="group relative overflow-hidden rounded-xl border p-4 text-left transition-all hover:shadow-md"
                    :class="activeFilter === 'published' ? 'border-emerald-200 bg-emerald-50 dark:border-emerald-800 dark:bg-emerald-900/20' : 'border-border bg-card hover:border-emerald-200'"
                    @click="applyFilter('published')"
                >
                    <p class="text-xs font-medium text-muted-foreground">Published</p>
                    <p class="mt-1 text-2xl font-bold" :class="activeFilter === 'published' ? 'text-emerald-700 dark:text-emerald-300' : ''">
                        {{ jobStore.loading ? '—' : publishedCount }}
                    </p>
                    <div
                        class="absolute right-3 top-3 flex h-7 w-7 items-center justify-center rounded-lg"
                        :class="activeFilter === 'published' ? 'bg-emerald-100 dark:bg-emerald-900/40' : 'bg-muted'"
                    >
                        <Globe class="h-3.5 w-3.5" :class="activeFilter === 'published' ? 'text-emerald-600' : 'text-muted-foreground'" />
                    </div>
                </button>

                <button
                    class="group relative overflow-hidden rounded-xl border p-4 text-left transition-all hover:shadow-md"
                    :class="activeFilter === 'draft' ? 'border-slate-300 bg-slate-50 dark:border-slate-600 dark:bg-slate-800/40' : 'border-border bg-card hover:border-slate-300'"
                    @click="applyFilter('draft')"
                >
                    <p class="text-xs font-medium text-muted-foreground">Drafts</p>
                    <p class="mt-1 text-2xl font-bold" :class="activeFilter === 'draft' ? 'text-slate-700 dark:text-slate-300' : ''">
                        {{ jobStore.loading ? '—' : draftCount }}
                    </p>
                    <div
                        class="absolute right-3 top-3 flex h-7 w-7 items-center justify-center rounded-lg"
                        :class="activeFilter === 'draft' ? 'bg-slate-200 dark:bg-slate-700' : 'bg-muted'"
                    >
                        <Clock class="h-3.5 w-3.5" :class="activeFilter === 'draft' ? 'text-slate-600 dark:text-slate-400' : 'text-muted-foreground'" />
                    </div>
                </button>

                <div class="relative overflow-hidden rounded-xl border border-border bg-card p-4">
                    <p class="text-xs font-medium text-muted-foreground">With Applicants</p>
                    <p class="mt-1 text-2xl font-bold">{{ jobStore.loading ? '—' : withApplications }}</p>
                    <div class="absolute right-3 top-3 flex h-7 w-7 items-center justify-center rounded-lg bg-muted">
                        <Users class="h-3.5 w-3.5 text-muted-foreground" />
                    </div>
                </div>
            </div>

            <!-- Search -->
            <Card class="border-0 shadow-sm">
                <CardHeader class="border-b pb-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <CardTitle class="text-sm font-semibold">
                            {{ activeFilter === 'all' ? 'All Jobs' : activeFilter === 'published' ? 'Published Jobs' : 'Draft Jobs' }}
                        </CardTitle>
                        <div class="relative w-full sm:w-72">
                            <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Search by title or location…"
                                class="w-full rounded-lg border border-border bg-background py-2 pl-9 pr-4 text-sm outline-none focus:border-violet-400 focus:ring-2 focus:ring-violet-100 dark:focus:ring-violet-900/40"
                                @keyup.enter="applySearch"
                                @input="search === '' && applySearch()"
                            />
                        </div>
                    </div>
                </CardHeader>

                <CardContent class="p-0">
                    <!-- Skeleton -->
                    <div v-if="jobStore.loading" class="divide-y">
                        <div v-for="i in 5" :key="i" class="flex items-center gap-4 px-6 py-4">
                            <div class="h-10 w-10 shrink-0 animate-pulse rounded-xl bg-muted" />
                            <div class="flex-1 space-y-2">
                                <div class="h-4 w-48 animate-pulse rounded bg-muted" />
                                <div class="h-3 w-64 animate-pulse rounded bg-muted/70" />
                            </div>
                            <div class="hidden gap-2 sm:flex">
                                <div class="h-6 w-20 animate-pulse rounded-full bg-muted" />
                                <div class="h-6 w-16 animate-pulse rounded-full bg-muted/70" />
                            </div>
                        </div>
                    </div>

                    <!-- Empty -->
                    <div v-else-if="!jobStore.jobs.length" class="flex flex-col items-center justify-center py-16 text-center">
                        <div class="mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-muted">
                            <Briefcase class="h-7 w-7 text-muted-foreground" />
                        </div>
                        <p class="text-sm font-medium">No job postings found</p>
                        <p class="mt-1 text-xs text-muted-foreground">
                            {{ activeFilter !== 'all' ? 'Try a different filter or ' : '' }}Post your first job to start receiving applications.
                        </p>
                        <Button class="mt-4 gap-2 bg-violet-600 hover:bg-violet-700 text-white" size="sm" as-child>
                            <Link href="/admin/jobs/create"><Plus class="h-3.5 w-3.5" /> Post a Job</Link>
                        </Button>
                    </div>

                    <!-- Job Rows -->
                    <div v-else class="divide-y">
                        <div
                            v-for="job in jobStore.jobs"
                            :key="job.uuid"
                            class="flex flex-col gap-3 px-6 py-4 transition-colors hover:bg-muted/30 sm:flex-row sm:items-center sm:justify-between"
                        >
                            <!-- Left: icon + info -->
                            <div class="flex items-start gap-4">
                                <div
                                    class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-linear-to-br text-white shadow-sm"
                                    :class="getJobIconGradient(job.title)"
                                >
                                    <Briefcase class="h-5 w-5" />
                                </div>
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="text-sm font-semibold leading-tight">{{ job.title }}</span>
                                        <Badge
                                            variant="outline"
                                            class="h-5 rounded-full px-2 py-0 text-[11px] font-medium capitalize"
                                            :class="statusStyles[job.status] ?? ''"
                                        >
                                            {{ job.status_label }}
                                        </Badge>
                                    </div>
                                    <div class="mt-1.5 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-muted-foreground">
                                        <span v-if="job.location" class="flex items-center gap-1">
                                            <MapPin class="h-3 w-3" />{{ job.location }}
                                        </span>
                                        <span v-if="job.employment_type_label" class="flex items-center gap-1">
                                            <Clock class="h-3 w-3" />{{ job.employment_type_label }}
                                        </span>
                                        <span
                                            v-if="job.deadline"
                                            class="flex items-center gap-1"
                                            :class="isPast(job.deadline) ? 'text-red-500' : isExpiringSoon(job.deadline) ? 'text-amber-500' : ''"
                                        >
                                            <Calendar class="h-3 w-3" />
                                            {{ isPast(job.deadline) ? 'Expired' : 'Closes' }} {{ formatDeadline(job.deadline) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: badges + actions -->
                            <div class="flex items-center gap-2 pl-14 sm:pl-0">
                                <!-- Employment type badge -->
                                <Badge
                                    v-if="job.employment_type"
                                    variant="secondary"
                                    class="hidden shrink-0 rounded-full px-2.5 py-0.5 text-[11px] font-medium capitalize sm:inline-flex"
                                    :class="employmentTypeColors[job.employment_type] ?? 'bg-slate-100 text-slate-600'"
                                >
                                    {{ job.employment_type_label }}
                                </Badge>

                                <!-- Applications count -->
                                <Badge
                                    v-if="(job.applications_count ?? 0) > 0"
                                    variant="secondary"
                                    class="shrink-0 gap-1 rounded-full bg-violet-100 px-2.5 py-0.5 text-[11px] font-medium text-violet-700 dark:bg-violet-900/30 dark:text-violet-300"
                                >
                                    <Users class="h-3 w-3" />
                                    {{ job.applications_count }}
                                </Badge>

                                <!-- Actions -->
                                <div class="ml-1 flex items-center gap-1">
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        class="h-8 px-2.5 text-xs"
                                        :class="job.status === 'published' ? 'hover:bg-amber-50 hover:text-amber-700 dark:hover:bg-amber-900/20' : 'hover:bg-emerald-50 hover:text-emerald-700 dark:hover:bg-emerald-900/20'"
                                        :disabled="jobStore.loading"
                                        @click="toggleStatus(job)"
                                    >
                                        {{ job.status === 'published' ? 'Unpublish' : 'Publish' }}
                                    </Button>
                                    <Button variant="ghost" size="sm" class="h-8 w-8 p-0 hover:bg-violet-50 hover:text-violet-700 dark:hover:bg-violet-900/20" as-child>
                                        <Link :href="`/admin/jobs/${job.uuid}/edit`">
                                            <Pencil class="h-3.5 w-3.5" />
                                        </Link>
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        class="h-8 w-8 p-0 text-muted-foreground hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/20"
                                        @click="deleteJob(job.uuid)"
                                    >
                                        <Trash2 class="h-3.5 w-3.5" />
                                    </Button>
                                </div>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
