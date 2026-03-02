<script setup lang="ts">
import { Briefcase, Mail, Phone, Search, User, UserPlus } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { useCandidateStore } from '@/stores/candidate.store';

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Candidates', href: '/admin/candidates' },
];

const candidateStore = useCandidateStore();
const search = ref('');

onMounted(() => candidateStore.fetchCandidates());

function applySearch() {
    candidateStore.fetchCandidates(search.value ? { search: search.value } : {});
}

function getInitials(name: string): string {
    return name
        .split(' ')
        .map((n) => n[0])
        .join('')
        .toUpperCase()
        .slice(0, 2);
}

const avatarColors = [
    'bg-violet-500',
    'bg-indigo-500',
    'bg-blue-500',
    'bg-rose-500',
    'bg-amber-500',
    'bg-emerald-500',
    'bg-pink-500',
];

function getAvatarColor(name: string): string {
    let hash = 0;
    for (let i = 0; i < name.length; i++) {
        hash = name.charCodeAt(i) + ((hash << 5) - hash);
    }
    return avatarColors[Math.abs(hash) % avatarColors.length];
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex-1 space-y-6 p-6">
            <!-- Page Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Candidates</h1>
                    <p class="mt-0.5 text-sm text-muted-foreground">
                        Browse and manage your talent pool.
                    </p>
                </div>
                <Button class="bg-violet-600 hover:bg-violet-700 text-white shadow-sm w-fit">
                    <UserPlus class="mr-2 h-4 w-4" />
                    Add Candidate
                </Button>
            </div>

            <!-- Stats Row -->
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                <Card class="border-0 shadow-sm bg-gradient-to-br from-violet-50 to-indigo-50 dark:from-violet-950/30 dark:to-indigo-950/30">
                    <CardContent class="p-4">
                        <p class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Total</p>
                        <p class="mt-1 text-2xl font-bold text-violet-700 dark:text-violet-400">
                            {{ candidateStore.loading ? '–' : candidateStore.candidates.length }}
                        </p>
                    </CardContent>
                </Card>
                <Card class="border-0 shadow-sm bg-gradient-to-br from-blue-50 to-cyan-50 dark:from-blue-950/30 dark:to-cyan-950/30">
                    <CardContent class="p-4">
                        <p class="text-xs font-medium text-muted-foreground uppercase tracking-wide">With Applications</p>
                        <p class="mt-1 text-2xl font-bold text-blue-700 dark:text-blue-400">
                            {{ candidateStore.candidates.filter((c) => (c.applications_count ?? 0) > 0).length }}
                        </p>
                    </CardContent>
                </Card>
                <Card class="border-0 shadow-sm bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-950/30 dark:to-teal-950/30 col-span-2 sm:col-span-1">
                    <CardContent class="p-4">
                        <p class="text-xs font-medium text-muted-foreground uppercase tracking-wide">New This Month</p>
                        <p class="mt-1 text-2xl font-bold text-emerald-700 dark:text-emerald-400">0</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Main Card -->
            <Card class="border-0 shadow-sm">
                <CardHeader class="flex-row items-center justify-between space-y-0 border-b pb-4">
                    <div>
                        <CardTitle class="text-base font-semibold">Candidate List</CardTitle>
                        <CardDescription>All registered candidates in your pipeline.</CardDescription>
                    </div>
                    <div class="relative">
                        <Search class="absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-muted-foreground" />
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Search candidates…"
                            class="h-9 w-56 rounded-lg border bg-muted/40 pl-9 pr-3 text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-violet-500/30"
                            @keyup.enter="applySearch"
                        />
                    </div>
                </CardHeader>
                <CardContent class="p-0">
                    <div v-if="candidateStore.loading" class="divide-y">
                        <div v-for="i in 5" :key="i" class="flex items-center gap-4 px-6 py-4 animate-pulse">
                            <div class="h-10 w-10 rounded-full bg-muted"></div>
                            <div class="flex-1 space-y-1.5">
                                <div class="h-3.5 w-36 rounded bg-muted"></div>
                                <div class="h-3 w-24 rounded bg-muted/70"></div>
                            </div>
                            <div class="h-6 w-16 rounded-full bg-muted"></div>
                        </div>
                    </div>

                    <div
                        v-else-if="!candidateStore.candidates.length"
                        class="flex flex-col items-center justify-center py-16 text-center"
                    >
                        <div class="mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-violet-50 dark:bg-violet-950/30">
                            <User class="h-7 w-7 text-violet-400" />
                        </div>
                        <p class="text-sm font-medium text-foreground">No candidates yet</p>
                        <p class="mt-1 text-xs text-muted-foreground">Candidates will appear here once they apply.</p>
                    </div>

                    <table v-else class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-muted/30">
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">Candidate</th>
                                <th class="hidden px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground sm:table-cell">Phone</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">Applications</th>
                                <th class="hidden px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground md:table-cell">Joined</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-muted-foreground">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border/60">
                            <tr
                                v-for="c in candidateStore.candidates"
                                :key="c.uuid"
                                class="group transition-colors hover:bg-muted/30"
                            >
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <Avatar class="h-9 w-9">
                                            <AvatarFallback :class="[getAvatarColor(c.name), 'text-white text-xs font-semibold']">
                                                {{ getInitials(c.name) }}
                                            </AvatarFallback>
                                        </Avatar>
                                        <div>
                                            <p class="font-medium text-foreground leading-tight">{{ c.name }}</p>
                                            <p class="text-xs text-muted-foreground flex items-center gap-1 mt-0.5">
                                                <Mail class="h-3 w-3" />{{ c.email }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="hidden px-6 py-4 sm:table-cell">
                                    <span v-if="c.phone" class="flex items-center gap-1.5 text-sm text-muted-foreground">
                                        <Phone class="h-3.5 w-3.5" />{{ c.phone }}
                                    </span>
                                    <span v-else class="text-xs text-muted-foreground/50">—</span>
                                </td>
                                <td class="px-6 py-4">
                                    <Badge
                                        variant="secondary"
                                        class="gap-1.5 font-medium"
                                        :class="(c.applications_count ?? 0) > 0 ? 'bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300' : ''"
                                    >
                                        <Briefcase class="h-3 w-3" />
                                        {{ c.applications_count ?? 0 }}
                                    </Badge>
                                </td>
                                <td class="hidden px-6 py-4 md:table-cell">
                                    <span class="text-sm text-muted-foreground">
                                        {{ new Date(c.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        class="h-8 px-3 text-xs opacity-0 group-hover:opacity-100 transition-opacity hover:bg-violet-50 hover:text-violet-700 dark:hover:bg-violet-950/30"
                                    >
                                        View Profile
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
