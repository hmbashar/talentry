<script setup lang="ts">
import { ArrowLeft, Briefcase, Calendar, Mail, Phone, User } from 'lucide-vue-next';
import { onMounted } from 'vue';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { useCandidateStore } from '@/stores/candidate.store';

const props = defineProps<{ uuid: string }>();
const candidateStore = useCandidateStore();

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Candidates', href: '/admin/candidates' },
    { title: 'Profile', href: `/admin/candidates/${props.uuid}` },
];

onMounted(() => candidateStore.fetchCandidate(props.uuid));

const avatarColors = ['bg-violet-500', 'bg-indigo-500', 'bg-blue-500', 'bg-rose-500', 'bg-amber-500', 'bg-emerald-500'];

function getAvatarColor(name: string): string {
    let hash = 0;
    for (let i = 0; i < name.length; i++) hash = name.charCodeAt(i) + ((hash << 5) - hash);
    return avatarColors[Math.abs(hash) % avatarColors.length];
}

function getInitials(name: string): string {
    return name.split(' ').map((n) => n[0]).join('').toUpperCase().slice(0, 2);
}

const statusStyles: Record<string, string> = {
    applied: 'bg-blue-100 text-blue-700 border-blue-200 dark:bg-blue-900/30 dark:text-blue-300',
    shortlisted: 'bg-yellow-100 text-yellow-700 border-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-300',
    interview: 'bg-violet-100 text-violet-700 border-violet-200 dark:bg-violet-900/30 dark:text-violet-300',
    rejected: 'bg-red-100 text-red-700 border-red-200 dark:bg-red-900/30 dark:text-red-300',
    hired: 'bg-emerald-100 text-emerald-700 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-300',
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex-1 space-y-6 p-6 max-w-5xl">
            <!-- Back -->
            <Button variant="ghost" size="sm" class="gap-2 text-muted-foreground hover:text-foreground -ml-2" as-child>
                <a href="/admin/candidates">
                    <ArrowLeft class="h-4 w-4" />
                    Back to Candidates
                </a>
            </Button>

            <!-- Loading -->
            <div v-if="candidateStore.loading" class="space-y-4 animate-pulse">
                <div class="flex items-center gap-4">
                    <div class="h-16 w-16 rounded-full bg-muted"></div>
                    <div class="space-y-2">
                        <div class="h-5 w-40 rounded bg-muted"></div>
                        <div class="h-3.5 w-28 rounded bg-muted/70"></div>
                    </div>
                </div>
                <div class="h-48 rounded-xl bg-muted"></div>
            </div>

            <!-- Candidate not found -->
            <div v-else-if="!candidateStore.candidate" class="flex flex-col items-center justify-center py-20">
                <div class="mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-muted">
                    <User class="h-7 w-7 text-muted-foreground" />
                </div>
                <p class="text-sm font-medium">Candidate not found</p>
            </div>

            <template v-else>
                <!-- Profile Header -->
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-4">
                        <Avatar class="h-16 w-16 text-xl">
                            <AvatarFallback :class="[getAvatarColor(candidateStore.candidate.name), 'text-white font-bold']">
                                {{ getInitials(candidateStore.candidate.name) }}
                            </AvatarFallback>
                        </Avatar>
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight">{{ candidateStore.candidate.name }}</h1>
                            <div class="mt-1 flex flex-wrap items-center gap-3 text-sm text-muted-foreground">
                                <span class="flex items-center gap-1"><Mail class="h-3.5 w-3.5" />{{ candidateStore.candidate.email }}</span>
                                <span v-if="candidateStore.candidate.phone" class="flex items-center gap-1">
                                    <Phone class="h-3.5 w-3.5" />{{ candidateStore.candidate.phone }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <Badge variant="secondary" class="gap-1.5 bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300">
                            <Briefcase class="h-3.5 w-3.5" />
                            {{ candidateStore.candidate.applications_count ?? 0 }} Application{{ (candidateStore.candidate.applications_count ?? 0) !== 1 ? 's' : '' }}
                        </Badge>
                    </div>
                </div>

                <div class="grid gap-6 md:grid-cols-3">
                    <!-- Contact Info -->
                    <Card class="border-0 shadow-sm md:col-span-1">
                        <CardHeader class="pb-3 border-b">
                            <CardTitle class="text-sm font-semibold">Contact Information</CardTitle>
                        </CardHeader>
                        <CardContent class="pt-4 space-y-3">
                            <div class="flex items-start gap-2.5">
                                <Mail class="mt-0.5 h-4 w-4 text-muted-foreground shrink-0" />
                                <div>
                                    <p class="text-xs text-muted-foreground">Email</p>
                                    <p class="text-sm font-medium">{{ candidateStore.candidate.email }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-2.5">
                                <Phone class="mt-0.5 h-4 w-4 text-muted-foreground shrink-0" />
                                <div>
                                    <p class="text-xs text-muted-foreground">Phone</p>
                                    <p class="text-sm font-medium">{{ candidateStore.candidate.phone ?? '—' }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-2.5">
                                <Calendar class="mt-0.5 h-4 w-4 text-muted-foreground shrink-0" />
                                <div>
                                    <p class="text-xs text-muted-foreground">Joined</p>
                                    <p class="text-sm font-medium">
                                        {{ new Date(candidateStore.candidate.created_at).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' }) }}
                                    </p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Applications -->
                    <Card class="border-0 shadow-sm md:col-span-2">
                        <CardHeader class="pb-3 border-b">
                            <CardTitle class="text-sm font-semibold">Applications</CardTitle>
                            <CardDescription>All job applications by this candidate.</CardDescription>
                        </CardHeader>
                        <CardContent class="p-0">
                            <div
                                v-if="!candidateStore.candidate.applications?.length"
                                class="flex flex-col items-center justify-center py-12 text-center"
                            >
                                <div class="mb-2 flex h-10 w-10 items-center justify-center rounded-xl bg-muted">
                                    <Briefcase class="h-5 w-5 text-muted-foreground" />
                                </div>
                                <p class="text-sm text-muted-foreground">No applications yet.</p>
                            </div>
                            <div v-else class="divide-y">
                                <div
                                    v-for="app in candidateStore.candidate.applications"
                                    :key="app.uuid"
                                    class="flex items-center justify-between px-6 py-4 hover:bg-muted/30 transition-colors"
                                >
                                    <div>
                                        <p class="text-sm font-medium">{{ app.job_posting?.title ?? 'Unknown Position' }}</p>
                                        <p class="text-xs text-muted-foreground mt-0.5">
                                            {{ new Date(app.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) }}
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <Badge variant="outline" class="capitalize font-medium" :class="statusStyles[app.status] ?? ''">
                                            {{ app.status_label ?? app.status }}
                                        </Badge>
                                        <Button variant="ghost" size="sm" class="h-8 px-3 text-xs hover:bg-violet-50 hover:text-violet-700" as-child>
                                            <a :href="`/admin/applications/${app.uuid}`">Review</a>
                                        </Button>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </template>
        </div>
    </AppLayout>
</template>
