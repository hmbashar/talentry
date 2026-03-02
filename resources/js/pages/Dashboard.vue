<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import {
    Activity,
    Briefcase,
    Calendar,
    ChevronRight,
    FileText,
    Plus,
    Users,
    Clock,
    PlayCircle
} from 'lucide-vue-next';
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
        id: 1,
        candidate: { name: 'Sarah Wilson', email: 'sarah.w@example.com', avatar: '' },
        job: 'Senior Frontend Developer',
        applied_at: '2 hours ago',
        status: 'reviewing',
    },
    {
        id: 2,
        candidate: { name: 'Michael Chen', email: 'm.chen@example.com', avatar: '' },
        job: 'Product Designer',
        applied_at: '4 hours ago',
        status: 'interview',
    },
    {
        id: 3,
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
                <Card class="col-span-4">
                    <CardHeader>
                        <CardTitle>Hiring Performance</CardTitle>
                        <CardDescription>Applications over time</CardDescription>
                    </CardHeader>
                    <CardContent class="pl-2">
                        <div class="h-[200px] w-full flex items-end justify-between space-x-2 px-4">
                             <!-- Simple CSS Bar Chart Placeholder -->
                             <div class="w-full bg-slate-100 rounded-md h-full relative overflow-hidden flex items-end justify-around py-2">
                                <div class="w-8 bg-blue-500 rounded-t-sm h-[40%] hover:opacity-80 transition-all cursor-pointer" title="Mon"></div>
                                <div class="w-8 bg-blue-500 rounded-t-sm h-[70%] hover:opacity-80 transition-all cursor-pointer" title="Tue"></div>
                                <div class="w-8 bg-blue-500 rounded-t-sm h-[50%] hover:opacity-80 transition-all cursor-pointer" title="Wed"></div>
                                <div class="w-8 bg-blue-500 rounded-t-sm h-[90%] hover:opacity-80 transition-all cursor-pointer" title="Thu"></div>
                                <div class="w-8 bg-blue-500 rounded-t-sm h-[60%] hover:opacity-80 transition-all cursor-pointer" title="Fri"></div>
                                <div class="w-8 bg-blue-500 rounded-t-sm h-[80%] hover:opacity-80 transition-all cursor-pointer" title="Sat"></div>
                                <div class="w-8 bg-blue-500 rounded-t-sm h-[55%] hover:opacity-80 transition-all cursor-pointer" title="Sun"></div>
                             </div>
                        </div>
                    </CardContent>
                </Card>
                <Card class="col-span-3">
                     <CardHeader>
                        <CardTitle>Recent Applications</CardTitle>
                        <CardDescription>
                            You have 3 new applications to review today.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-8">
                            <div v-for="application in recentApplications" :key="application.id" class="flex items-center">
                                <Avatar class="h-9 w-9">
                                    <AvatarImage :src="application.candidate.avatar" :alt="application.candidate.name" />
                                    <AvatarFallback>{{ application.candidate.name.split(' ').map(n => n[0]).join('') }}</AvatarFallback>
                                </Avatar>
                                <div class="ml-4 space-y-1">
                                    <p class="text-sm font-medium leading-none">{{ application.candidate.name }}</p>
                                    <p class="text-sm text-muted-foreground">
                                        Applied for {{ application.job }}
                                    </p>
                                </div>
                                <div class="ml-auto font-medium flex items-center space-x-2">
                                    <Badge variant="outline" :class="getStatusColor(application.status)">
                                        {{ application.status.charAt(0).toUpperCase() + application.status.slice(1) }}
                                    </Badge>
                                    <Button variant="ghost" size="icon" title="Video Details">
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
