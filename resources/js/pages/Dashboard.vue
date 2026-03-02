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

import DashboardAreaChart from '@/components/DashboardAreaChart.vue'; // New component
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import admin from '@/routes/admin';
import type { BreadcrumbItem, User } from '@/types';

const page = usePage();
const user = page.props.auth.user as User;

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
    },
];

// Mock data for display purposes
const recentApplications = [
    {
        uuid: '1111-2222-3333-4444',
        candidate: { name: 'Sarah Wilson', email: 'sarah.w@example.com', avatar: '' },
        job: 'Senior Frontend Developer',
        applied_at: '2 hours ago',
        status: 'reviewing',
    },
    {
        uuid: '5555-6666-7777-8888',
        candidate: { name: 'Michael Chen', email: 'm.chen@example.com', avatar: '' },
        job: 'Product Designer',
        applied_at: '4 hours ago',
        status: 'interview',
    },
    {
        uuid: '9999-0000-aaaa-bbbb',
        candidate: { name: 'Emily Davis', email: 'emily.d@example.com', avatar: '' },
        job: 'Senior Frontend Developer',
        applied_at: '1 day ago',
        status: 'rejected',
    },
];

const upcomingInterviews = [
    {
        id: 1,
        candidate: 'Michael Chen',
        role: 'Product Designer',
        time: 'Today, 2:00 PM',
        type: 'Technical Interview',
    },
    {
        id: 2,
        candidate: 'Alex Johnson',
        role: 'Backend Engineer',
        time: 'Tomorrow, 10:00 AM',
        type: 'Cultural Fit',
    },
];

const getStatusColor = (status: string) => {
    switch (status) {
        case 'reviewing': return 'bg-yellow-100 text-yellow-800 border-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-300 dark:border-yellow-900';
        case 'interview': return 'bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-900';
        case 'rejected': return 'bg-red-100 text-red-800 border-red-200 dark:bg-red-900/30 dark:text-red-300 dark:border-red-900';
        case 'highlighted': return 'bg-green-100 text-green-800 border-green-200 dark:bg-green-900/30 dark:text-green-300 dark:border-green-900';
        default: return 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300';
    }
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex-1 space-y-4 p-4 pt-6">
            <div class="flex items-center justify-between space-y-2">
                <div>
                    <h2 class="text-3xl font-bold tracking-tight">Dashboard</h2>
                    <p class="text-muted-foreground">
                        Welcome back, {{ user.name }}. Here's an overview of your hiring pipeline.
                    </p>
                </div>
                <div class="flex items-center space-x-2">
                    <Button as-child>
                        <Link :href="admin.jobs.create()">
                            <Plus class="mr-2 h-4 w-4" />
                            Post a Job
                        </Link>
                    </Button>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <Card class="bg-indigo-600 text-white border-0 shadow-lg relative overflow-hidden">
                    <div class="absolute right-0 top-0 h-full w-24 bg-white/10 -skew-x-12 translate-x-10"></div>
                     <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2 relative z-10">
                        <CardTitle class="text-sm font-medium text-white/90">
                            Total Applications
                        </CardTitle>
                        <FileText class="h-4 w-4 text-white/80" />
                    </CardHeader>
                    <CardContent class="relative z-10">
                        <div class="text-3xl font-bold">24</div>
                        <p class="text-xs text-indigo-100 flex items-center mt-1">
                            <span class="bg-indigo-500/50 rounded px-1 text-[10px] mr-1">+12%</span> from last month
                        </p>
                    </CardContent>
                </Card>
                <Card class="bg-violet-600 text-white border-0 shadow-lg relative overflow-hidden">
                    <div class="absolute right-0 top-0 h-full w-24 bg-white/10 -skew-x-12 translate-x-10"></div>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2 relative z-10">
                        <CardTitle class="text-sm font-medium text-white/90">
                            Active Jobs
                        </CardTitle>
                        <Briefcase class="h-4 w-4 text-white/80" />
                    </CardHeader>
                    <CardContent class="relative z-10">
                        <div class="text-3xl font-bold">7</div>
                        <p class="text-xs text-violet-100 flex items-center mt-1">
                            <span class="bg-violet-500/50 rounded px-1 text-[10px] mr-1">2 closing</span> this week
                        </p>
                    </CardContent>
                </Card>
                <Card class="bg-amber-500 text-white border-0 shadow-lg relative overflow-hidden">
                    <div class="absolute right-0 top-0 h-full w-24 bg-white/10 -skew-x-12 translate-x-10"></div>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2 relative z-10">
                        <CardTitle class="text-sm font-medium text-white/90">
                            Candidates
                        </CardTitle>
                        <Users class="h-4 w-4 text-white/80" />
                    </CardHeader>
                    <CardContent class="relative z-10">
                        <div class="text-3xl font-bold">128</div>
                        <p class="text-xs text-amber-100 flex items-center mt-1">
                            <span class="bg-amber-600/50 rounded px-1 text-[10px] mr-1">+4</span> new today
                        </p>
                    </CardContent>
                </Card>
                <Card class="bg-rose-500 text-white border-0 shadow-lg relative overflow-hidden">
                    <div class="absolute right-0 top-0 h-full w-24 bg-white/10 -skew-x-12 translate-x-10"></div>
                     <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2 relative z-10">
                        <CardTitle class="text-sm font-medium text-white/90">
                            Interviews
                        </CardTitle>
                        <Activity class="h-4 w-4 text-white/80" />
                    </CardHeader>
                    <CardContent class="relative z-10">
                        <div class="text-3xl font-bold">8</div>
                        <p class="text-xs text-rose-100 flex items-center mt-1">
                            Scheduled for this week
                        </p>
                    </CardContent>
                </Card>
            </div>

            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-7">
                <Card class="col-span-4 shadow-lg border-0">
                    <CardHeader>
                        <CardTitle>Hiring Overview</CardTitle>
                        <CardDescription>Application trends over the last 7 days</CardDescription>
                    </CardHeader>
                    <CardContent class="pl-2">
                        <DashboardAreaChart
                            :data="[12, 18, 15, 25, 20, 32, 28]"
                            :labels="['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']"
                            color="#8b5cf6"
                            :height="300"
                        />
                    </CardContent>
                </Card>
                <Card class="col-span-3 shadow-lg border-0">
                     <CardHeader>
                        <CardTitle>Recent Applications</CardTitle>
                        <CardDescription>
                            You have 3 new applications to review today.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-8">
                            <div v-for="application in recentApplications" :key="application.uuid" class="flex items-center">
                                <Avatar class="h-9 w-9">
                                    <AvatarImage :src="application.candidate.avatar" :alt="application.candidate.name" />
                                    <AvatarFallback>{{ application.candidate.name.split(' ').map(n => n[0]).join('') }}</AvatarFallback>
                                </Avatar>
                                <div class="ml-4 space-y-1">
                                    <p class="text-sm font-medium leading-none">{{ application.candidate.name }}</p>
                                    <p class="text-xs text-muted-foreground">
                                        {{ application.job }} • <span class="text-xs text-muted-foreground">{{ application.applied_at }}</span>
                                    </p>
                                </div>
                                <div class="ml-auto font-medium flex items-center space-x-2">
                                    <Badge variant="outline" :class="getStatusColor(application.status)">
                                        {{ application.status.charAt(0).toUpperCase() + application.status.slice(1) }}
                                    </Badge>
                                    <Button variant="ghost" size="icon" title="View Application" as-child>
                                        <Link :href="admin.applications.show({ uuid: application.uuid })">
                                            <Eye class="h-4 w-4 text-muted-foreground hover:text-primary" />
                                        </Link>
                                    </Button>
                                    <Button variant="ghost" size="icon" title="Video Introduction">
                                        <PlayCircle class="h-4 w-4 text-muted-foreground hover:text-primary" />
                                    </Button>
                                </div>
                            </div>
                        </div>
                        <div class="mt-6">
                            <Button variant="outline" class="w-full" as-child>
                                <Link :href="admin.applications.index()">
                                    View all applications
                                    <ChevronRight class="ml-2 h-4 w-4" />
                                </Link>
                            </Button>
                        </div>
                    </CardContent>
                </Card>
                <Card class="col-span-3">
                    <CardHeader>
                        <CardTitle>Upcoming Interviews</CardTitle>
                        <CardDescription>
                            Your schedule for the next few days.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-8">
                            <div v-for="interview in upcomingInterviews" :key="interview.id" class="flex items-start">
                                <div class="rounded-full bg-blue-100 p-2 dark:bg-blue-900/20">
                                    <Calendar class="h-4 w-4 text-blue-600 dark:text-blue-400" />
                                </div>
                                <div class="ml-4 space-y-1">
                                    <p class="text-sm font-medium leading-none">{{ interview.candidate }}</p>
                                    <p class="text-xs text-muted-foreground">{{ interview.role }} • {{ interview.type }}</p>
                                    <p class="text-xs font-semibold text-primary">{{ interview.time }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 pt-6 border-t">
                            <h4 class="text-sm font-medium mb-4">Quick Actions</h4>
                            <div class="grid grid-cols-2 gap-2">
                                <Button variant="outline" size="sm" class="justify-start">
                                    <Users class="mr-2 h-4 w-4" />
                                    New Candidate
                                </Button>
                                <Button variant="outline" size="sm" class="justify-start">
                                    <Clock class="mr-2 h-4 w-4" />
                                    Schedule
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
