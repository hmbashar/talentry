<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import {
    Briefcase,
    ChevronRight,
    FileText,
    Plus,
    Users,
    Activity,
    ArrowUpRight,
    Eye,
} from 'lucide-vue-next';
import { onMounted, computed } from 'vue';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import DashboardAreaChart from '@/components/DashboardAreaChart.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import admin from '@/routes/admin';
import { useDashboardStore } from '@/stores/dashboard.store';
import type { BreadcrumbItem, User } from '@/types';

const page = usePage();
const user = page.props.auth.user as User;

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Dashboard', href: dashboard() }];

const dashboardStore = useDashboardStore();
onMounted(() => dashboardStore.fetchStats());

const stats = computed(() => dashboardStore.stats);

const pipeline = computed(() => {
    const byStatus = stats.value?.applications_by_status ?? {};
    const total = stats.value?.total_applications ?? 1;
    const items = [
        { key: 'applied', label: 'Applied', color: 'bg-blue-500', textColor: 'text-blue-600 dark:text-blue-400' },
        { key: 'shortlisted', label: 'Shortlisted', color: 'bg-amber-400', textColor: 'text-amber-600 dark:text-amber-400' },
        { key: 'interview', label: 'Interview', color: 'bg-violet-500', textColor: 'text-violet-600 dark:text-violet-400' },
        { key: 'hired', label: 'Hired', color: 'bg-emerald-500', textColor: 'text-emerald-600 dark:text-emerald-400' },
        { key: 'rejected', label: 'Rejected', color: 'bg-red-400', textColor: 'text-red-500 dark:text-red-400' },
    ];
    return items.map((item) => ({
        ...item,
        count: byStatus[item.key] ?? 0,
        pct: total > 0 ? Math.round(((byStatus[item.key] ?? 0) / total) * 100) : 0,
    }));
});

const statusStyles: Record<string, string> = {
    applied: 'bg-blue-100 text-blue-700 border-blue-200 dark:bg-blue-900/30 dark:text-blue-300',
    shortlisted: 'bg-amber-100 text-amber-700 border-amber-200 dark:bg-amber-900/30 dark:text-amber-300',
    interview: 'bg-violet-100 text-violet-700 border-violet-200 dark:bg-violet-900/30 dark:text-violet-300',
    hired: 'bg-emerald-100 text-emerald-700 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-300',
    rejected: 'bg-red-100 text-red-700 border-red-200 dark:bg-red-900/30 dark:text-red-300',
};

function getInitials(name: string): string {
    return name.split(' ').map((n) => n[0]).join('').toUpperCase().slice(0, 2);
}

const avatarColors = ['bg-violet-500', 'bg-indigo-500', 'bg-blue-500', 'bg-rose-500', 'bg-amber-500', 'bg-emerald-500'];
function getAvatarColor(name: string): string {
    let hash = 0;
    for (let i = 0; i < name.length; i++) hash = name.charCodeAt(i) + ((hash << 5) - hash);
    return avatarColors[Math.abs(hash) % avatarColors.length];
}
</script>

<template>
    <Head title="Dashboard" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex-1 space-y-6 p-6">

            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight">Dashboard</h2>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Welcome back, <span class="font-medium text-foreground">{{ user.name }}</span>. Here's your hiring overview.
                    </p>
                </div>
                <Button class="gap-2 bg-violet-600 hover:bg-violet-700 text-white shadow-sm" as-child>
                    <Link :href="admin.jobs.create()">
                        <Plus class="h-4 w-4" />
                        Post a Job
                    </Link>
                </Button>
            </div>

            <!-- Stat Cards -->
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Total Applications -->
                <Card class="relative overflow-hidden border-0 bg-indigo-600 text-white shadow-lg">
                    <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-white/10"></div>
                    <div class="absolute -bottom-4 -right-2 h-16 w-16 rounded-full bg-white/5"></div>
                    <CardHeader class="relative z-10 flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-sm font-medium text-white/90">Total Applications</CardTitle>
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/15">
                            <FileText class="h-4 w-4 text-white" />
                        </div>
                    </CardHeader>
                    <CardContent class="relative z-10">
                        <div class="text-3xl font-bold">
                            <span v-if="dashboardStore.loading" class="inline-block h-8 w-12 animate-pulse rounded bg-white/20" />
                            <span v-else>{{ stats?.total_applications ?? 0 }}</span>
                        </div>
                        <p class="mt-1.5 flex items-center gap-1 text-xs text-indigo-100">
                            <ArrowUpRight class="h-3 w-3" /> All time applications
                        </p>
                    </CardContent>
                </Card>

                <!-- Active Jobs -->
                <Card class="relative overflow-hidden border-0 bg-violet-600 text-white shadow-lg">
                    <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-white/10"></div>
                    <div class="absolute -bottom-4 -right-2 h-16 w-16 rounded-full bg-white/5"></div>
                    <CardHeader class="relative z-10 flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-sm font-medium text-white/90">Active Jobs</CardTitle>
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/15">
                            <Briefcase class="h-4 w-4 text-white" />
                        </div>
                    </CardHeader>
                    <CardContent class="relative z-10">
                        <div class="text-3xl font-bold">
                            <span v-if="dashboardStore.loading" class="inline-block h-8 w-12 animate-pulse rounded bg-white/20" />
                            <span v-else>{{ stats?.published_jobs ?? 0 }}</span>
                        </div>
                        <p class="mt-1.5 flex items-center gap-1 text-xs text-violet-100">
                            <ArrowUpRight class="h-3 w-3" />
                            {{ stats?.draft_jobs ?? 0 }} in draft
                        </p>
                    </CardContent>
                </Card>

                <!-- Candidates -->
                <Card class="relative overflow-hidden border-0 bg-amber-500 text-white shadow-lg">
                    <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-white/10"></div>
                    <div class="absolute -bottom-4 -right-2 h-16 w-16 rounded-full bg-white/5"></div>
                    <CardHeader class="relative z-10 flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-sm font-medium text-white/90">Candidates</CardTitle>
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/15">
                            <Users class="h-4 w-4 text-white" />
                        </div>
                    </CardHeader>
                    <CardContent class="relative z-10">
                        <div class="text-3xl font-bold">
                            <span v-if="dashboardStore.loading" class="inline-block h-8 w-12 animate-pulse rounded bg-white/20" />
                            <span v-else>{{ stats?.total_candidates ?? 0 }}</span>
                        </div>
                        <p class="mt-1.5 flex items-center gap-1 text-xs text-amber-100">
                            <ArrowUpRight class="h-3 w-3" /> In talent pool
                        </p>
                    </CardContent>
                </Card>

                <!-- Interviews -->
                <Card class="relative overflow-hidden border-0 bg-rose-500 text-white shadow-lg">
                    <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-white/10"></div>
                    <div class="absolute -bottom-4 -right-2 h-16 w-16 rounded-full bg-white/5"></div>
                    <CardHeader class="relative z-10 flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-sm font-medium text-white/90">Interviews</CardTitle>
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/15">
                            <Activity class="h-4 w-4 text-white" />
                        </div>
                    </CardHeader>
                    <CardContent class="relative z-10">
                        <div class="text-3xl font-bold">
                            <span v-if="dashboardStore.loading" class="inline-block h-8 w-12 animate-pulse rounded bg-white/20" />
                            <span v-else>{{ stats?.interview_count ?? 0 }}</span>
                        </div>
                        <p class="mt-1.5 flex items-center gap-1 text-xs text-rose-100">
                            <ArrowUpRight class="h-3 w-3" /> At interview stage
                        </p>
                    </CardContent>
                </Card>
            </div>

            <!-- Bottom Section -->
            <div class="grid gap-5 lg:grid-cols-7">

                <!-- Area Chart -->
                <Card class="border-0 shadow-sm lg:col-span-4">
                    <CardHeader class="border-b pb-4">
                        <CardTitle class="text-sm font-semibold">Application Trends</CardTitle>
                        <CardDescription>Illustrative trend over the last 7 days</CardDescription>
                    </CardHeader>
                    <CardContent class="pt-4">
                        <DashboardAreaChart
                            :data="[12, 18, 15, 25, 20, 32, 28]"
                            :labels="['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']"
                            color="#8b5cf6"
                            :height="220"
                        />
                    </CardContent>
                </Card>

                <!-- Hiring Pipeline -->
                <Card class="border-0 shadow-sm lg:col-span-3">
                    <CardHeader class="border-b pb-4">
                        <CardTitle class="text-sm font-semibold">Hiring Pipeline</CardTitle>
                        <CardDescription>Application breakdown by current stage</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4 pt-5">
                        <!-- Skeleton -->
                        <div v-if="dashboardStore.loading" class="space-y-4">
                            <div v-for="i in 5" :key="i" class="space-y-1.5 animate-pulse">
                                <div class="flex justify-between">
                                    <div class="h-3.5 w-20 rounded bg-muted" />
                                    <div class="h-3.5 w-8 rounded bg-muted/70" />
                                </div>
                                <div class="h-2 w-full rounded-full bg-muted" />
                            </div>
                        </div>

                        <template v-else>
                            <div v-for="stage in pipeline" :key="stage.key" class="space-y-1.5">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-medium" :class="stage.textColor">{{ stage.label }}</span>
                                    <span class="text-muted-foreground">{{ stage.count }} <span class="text-muted-foreground/60">({{ stage.pct }}%)</span></span>
                                </div>
                                <div class="h-2 w-full overflow-hidden rounded-full bg-muted">
                                    <div
                                        class="h-full rounded-full transition-all duration-700"
                                        :class="stage.color"
                                        :style="{ width: `${stage.pct}%` }"
                                    />
                                </div>
                            </div>

                            <!-- Total -->
                            <div class="mt-4 flex items-center justify-between border-t pt-4 text-xs text-muted-foreground">
                                <span>Total applications</span>
                                <span class="font-semibold text-foreground">{{ stats?.total_applications ?? 0 }}</span>
                            </div>
                        </template>
                    </CardContent>
                </Card>

                <!-- Recent Applications -->
                <Card class="border-0 shadow-sm lg:col-span-7">
                    <CardHeader class="border-b pb-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <CardTitle class="text-sm font-semibold">Recent Applications</CardTitle>
                                <CardDescription class="mt-0.5">Latest candidates who applied</CardDescription>
                            </div>
                            <Button variant="ghost" size="sm" class="gap-1 text-xs text-muted-foreground hover:text-foreground" as-child>
                                <Link :href="admin.applications.index()">
                                    View all <ChevronRight class="h-3.5 w-3.5" />
                                </Link>
                            </Button>
                        </div>
                    </CardHeader>
                    <CardContent class="p-0">
                        <!-- Skeleton -->
                        <div v-if="dashboardStore.loading" class="divide-y">
                            <div v-for="i in 4" :key="i" class="flex items-center gap-4 px-6 py-4 animate-pulse">
                                <div class="h-9 w-9 shrink-0 rounded-full bg-muted" />
                                <div class="flex-1 space-y-2">
                                    <div class="h-3.5 w-36 rounded bg-muted" />
                                    <div class="h-3 w-52 rounded bg-muted/70" />
                                </div>
                                <div class="h-5 w-20 rounded-full bg-muted" />
                            </div>
                        </div>

                        <!-- Empty -->
                        <div v-else-if="!stats?.recent_applications?.length" class="flex flex-col items-center justify-center py-12 text-center">
                            <div class="mb-2 flex h-10 w-10 items-center justify-center rounded-xl bg-muted">
                                <FileText class="h-5 w-5 text-muted-foreground" />
                            </div>
                            <p class="text-sm text-muted-foreground">No applications yet.</p>
                        </div>

                        <!-- Rows -->
                        <div v-else class="divide-y">
                            <div
                                v-for="app in stats.recent_applications"
                                :key="app.uuid"
                                class="flex items-center gap-4 px-6 py-3.5 transition-colors hover:bg-muted/30"
                            >
                                <!-- Avatar -->
                                <Avatar class="h-9 w-9 shrink-0 text-sm">
                                    <AvatarFallback :class="[getAvatarColor(app.candidate?.name ?? ''), 'text-white font-semibold']">
                                        {{ getInitials(app.candidate?.name ?? '?') }}
                                    </AvatarFallback>
                                </Avatar>

                                <!-- Name + job -->
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium">{{ app.candidate?.name }}</p>
                                    <p class="truncate text-xs text-muted-foreground">
                                        {{ app.job }} &middot; {{ app.created_at }}
                                    </p>
                                </div>

                                <!-- Status badge -->
                                <Badge
                                    variant="outline"
                                    class="shrink-0 rounded-full px-2.5 py-0.5 text-[11px] font-medium capitalize"
                                    :class="statusStyles[app.status] ?? ''"
                                >
                                    {{ app.status_label }}
                                </Badge>

                                <!-- View button -->
                                <Button variant="ghost" size="sm" class="h-8 w-8 shrink-0 p-0 hover:bg-violet-50 hover:text-violet-700 dark:hover:bg-violet-900/20" as-child>
                                    <Link :href="admin.applications.show({ uuid: app.uuid })">
                                        <Eye class="h-3.5 w-3.5" />
                                    </Link>
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

        </div>
    </AppLayout>
</template>
