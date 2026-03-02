<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Briefcase, ClipboardList, Eye, Search } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import admin from '@/routes/admin';
import { useApplicationStore } from '@/stores/application.store';

const applicationStore = useApplicationStore();
const statusFilter = ref('');
const search = ref('');

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Applications', href: '/admin/applications' },
];

const statusStyles: Record<string, string> = {
    applied: 'bg-blue-100 text-blue-700 border-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800',
    shortlisted: 'bg-yellow-100 text-yellow-700 border-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-300 dark:border-yellow-800',
    interview: 'bg-violet-100 text-violet-700 border-violet-200 dark:bg-violet-900/30 dark:text-violet-300 dark:border-violet-800',
    rejected: 'bg-red-100 text-red-700 border-red-200 dark:bg-red-900/30 dark:text-red-300 dark:border-red-800',
    hired: 'bg-emerald-100 text-emerald-700 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-300 dark:border-emerald-800',
};

const statCounts: Record<string, string> = {
    applied: 'bg-blue-50 text-blue-700 dark:bg-blue-950/30 dark:text-blue-400',
    interview: 'bg-violet-50 text-violet-700 dark:bg-violet-950/30 dark:text-violet-400',
    hired: 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400',
};

onMounted(() => applicationStore.fetchApplications());

function applyFilter(status: string) {
    statusFilter.value = status;
    applicationStore.fetchApplications(status ? { status } : {});
}

function getInitials(name: string): string {
    return name?.split(' ').map((n) => n[0]).join('').toUpperCase().slice(0, 2) ?? '??';
}

const avatarColors = ['bg-violet-500', 'bg-indigo-500', 'bg-blue-500', 'bg-rose-500', 'bg-amber-500', 'bg-emerald-500'];

function getAvatarColor(name: string): string {
    let hash = 0;
    for (let i = 0; i < (name ?? '').length; i++) hash = name.charCodeAt(i) + ((hash << 5) - hash);
    return avatarColors[Math.abs(hash) % avatarColors.length];
}

const filteredApplications = () => {
    if (!search.value) return applicationStore.applications;
    const q = search.value.toLowerCase();
    return applicationStore.applications.filter(
        (a) => a.candidate?.name?.toLowerCase().includes(q) || a.job_posting?.title?.toLowerCase().includes(q),
    );
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex-1 space-y-6 p-6">
            <!-- Page Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Applications</h1>
                    <p class="mt-0.5 text-sm text-muted-foreground">Review and manage all candidate applications.</p>
                </div>
            </div>

            <!-- Stats Row -->
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                <Card
                    v-for="(style, key) in statCounts"
                    :key="key"
                    class="border-0 shadow-sm cursor-pointer transition-all"
                    :class="[style, statusFilter === key ? 'ring-2 ring-violet-400' : 'hover:shadow-md']"
                    @click="applyFilter(key)"
                >
                    <CardContent class="p-4">
                        <p class="text-xs font-medium uppercase tracking-wide opacity-70">{{ key.charAt(0).toUpperCase() + key.slice(1) }}</p>
                        <p class="mt-1 text-2xl font-bold">
                            {{ applicationStore.applications.filter((a) => a.status === key).length }}
                        </p>
                    </CardContent>
                </Card>
                <Card
                    class="border-0 shadow-sm col-span-2 sm:col-span-1 bg-gradient-to-br from-slate-50 to-gray-50 dark:from-slate-950/30 dark:to-gray-950/30 cursor-pointer"
                    :class="statusFilter === '' ? 'ring-2 ring-slate-400' : 'hover:shadow-md'"
                    @click="applyFilter('')"
                >
                    <CardContent class="p-4">
                        <p class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Total</p>
                        <p class="mt-1 text-2xl font-bold">{{ applicationStore.loading ? '–' : applicationStore.applications.length }}</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Main Card -->
            <Card class="border-0 shadow-sm">
                <CardHeader class="flex-row items-center justify-between space-y-0 border-b pb-4">
                    <div>
                        <CardTitle class="text-base font-semibold">Application List</CardTitle>
                        <CardDescription>
                            {{ statusFilter ? `Showing "${statusFilter}" applications` : 'All applications in your pipeline.' }}
                        </CardDescription>
                    </div>
                    <!-- Search & Filter -->
                    <div class="flex items-center gap-2">
                        <div class="relative">
                            <Search class="absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-muted-foreground" />
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Search applications…"
                                class="h-9 w-48 rounded-lg border bg-muted/40 pl-9 pr-3 text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-violet-500/30"
                            />
                        </div>
                    </div>
                </CardHeader>
                <CardContent class="p-0">
                    <!-- Loader -->
                    <div v-if="applicationStore.loading" class="divide-y">
                        <div v-for="i in 5" :key="i" class="flex items-center gap-4 px-6 py-4 animate-pulse">
                            <div class="h-10 w-10 rounded-full bg-muted"></div>
                            <div class="flex-1 space-y-1.5">
                                <div class="h-3.5 w-48 rounded bg-muted"></div>
                                <div class="h-3 w-28 rounded bg-muted/70"></div>
                            </div>
                            <div class="h-6 w-20 rounded-full bg-muted"></div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div
                        v-else-if="!filteredApplications().length"
                        class="flex flex-col items-center justify-center py-16 text-center"
                    >
                        <div class="mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-violet-50 dark:bg-violet-950/30">
                            <ClipboardList class="h-7 w-7 text-violet-400" />
                        </div>
                        <p class="text-sm font-medium text-foreground">No applications found</p>
                        <p class="mt-1 text-xs text-muted-foreground">Try adjusting your filters or search query.</p>
                    </div>

                    <!-- Table -->
                    <table v-else class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-muted/30">
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">Candidate</th>
                                <th class="hidden px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground md:table-cell">Position</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">Status</th>
                                <th class="hidden px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground sm:table-cell">Applied</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-muted-foreground">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border/60">
                            <tr
                                v-for="app in filteredApplications()"
                                :key="app.uuid"
                                class="group transition-colors hover:bg-muted/30"
                            >
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <Avatar class="h-9 w-9">
                                            <AvatarFallback :class="[getAvatarColor(app.candidate?.name ?? ''), 'text-white text-xs font-semibold']">
                                                {{ getInitials(app.candidate?.name ?? '') }}
                                            </AvatarFallback>
                                        </Avatar>
                                        <div>
                                            <p class="font-medium text-foreground leading-tight">{{ app.candidate?.name }}</p>
                                            <p class="text-xs text-muted-foreground mt-0.5">{{ app.candidate?.email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="hidden px-6 py-4 md:table-cell">
                                    <div class="flex items-center gap-1.5 text-sm text-muted-foreground">
                                        <Briefcase class="h-3.5 w-3.5 shrink-0" />
                                        <span>{{ app.job_posting?.title ?? '—' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <Badge
                                        variant="outline"
                                        class="font-medium capitalize"
                                        :class="statusStyles[app.status] ?? 'bg-gray-100 text-gray-600'"
                                    >
                                        {{ app.status_label ?? app.status }}
                                    </Badge>
                                </td>
                                <td class="hidden px-6 py-4 sm:table-cell">
                                    <span class="text-sm text-muted-foreground">
                                        {{ new Date(app.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        class="h-8 px-3 text-xs opacity-0 group-hover:opacity-100 transition-opacity hover:bg-violet-50 hover:text-violet-700 dark:hover:bg-violet-950/30"
                                        as-child
                                    >
                                        <Link :href="admin.applications.show({ uuid: app.uuid })">
                                            <Eye class="mr-1.5 h-3.5 w-3.5" />
                                            Review
                                        </Link>
                                    </Button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

