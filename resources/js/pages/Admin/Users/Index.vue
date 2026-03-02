<script setup lang="ts">
import { Mail, Search, Shield, ShieldCheck, ShieldOff, User } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import api from '@/lib/api';

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Users', href: '/admin/users' },
];

interface UserItem {
    id: number;
    name: string;
    email: string;
    role: 'admin' | 'recruiter';
    role_label: string;
    created_at: string;
}

const users = ref<UserItem[]>([]);
const meta = ref<{ total: number; current_page: number; last_page: number } | null>(null);
const loading = ref(false);
const search = ref('');
const roleFilter = ref<'' | 'admin' | 'recruiter'>('');
const updatingRole = ref<number | null>(null);

async function fetchUsers() {
    loading.value = true;
    try {
        const params: Record<string, string> = {};
        if (search.value) params.search = search.value;
        if (roleFilter.value) params.role = roleFilter.value;
        const { data } = await api.get('/users', { params });
        users.value = data.data;
        meta.value = data.meta;
    } finally {
        loading.value = false;
    }
}

async function toggleRole(user: UserItem) {
    updatingRole.value = user.id;
    try {
        const newRole = user.role === 'admin' ? 'recruiter' : 'admin';
        const { data } = await api.patch(`/users/${user.id}/role`, { role: newRole });
        const idx = users.value.findIndex((u) => u.id === user.id);
        if (idx !== -1) {
            users.value[idx].role = data.data.role;
            users.value[idx].role_label = data.data.role_label;
        }
    } finally {
        updatingRole.value = null;
    }
}

function getInitials(name: string): string {
    return name.split(' ').map((n) => n[0]).join('').toUpperCase().slice(0, 2);
}

const avatarColors = ['bg-violet-500', 'bg-indigo-500', 'bg-blue-500', 'bg-rose-500', 'bg-amber-500', 'bg-emerald-500'];
function getAvatarColor(name: string): string {
    let hash = 0;
    for (let i = 0; i < name.length; i++) hash = name.charCodeAt(i) + ((hash << 5) - hash);
    return avatarColors[Math.abs(hash) % avatarColors.length];
}

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}

onMounted(() => fetchUsers());
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex-1 space-y-6 p-6">
            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Users</h1>
                    <p class="mt-1 text-sm text-muted-foreground">Manage team members and their roles</p>
                </div>
                <div class="flex items-center gap-2">
                    <Badge variant="secondary" class="gap-1.5 bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300">
                        <ShieldCheck class="h-3.5 w-3.5" />
                        {{ meta?.total ?? '—' }} total users
                    </Badge>
                </div>
            </div>

            <!-- Stat tabs -->
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                <button
                    class="relative overflow-hidden rounded-xl border p-4 text-left transition-all hover:shadow-md"
                    :class="roleFilter === '' ? 'border-violet-200 bg-violet-50 dark:border-violet-800 dark:bg-violet-900/20' : 'border-border bg-card hover:border-violet-200'"
                    @click="roleFilter = ''; fetchUsers()"
                >
                    <p class="text-xs font-medium text-muted-foreground">All Users</p>
                    <p class="mt-1 text-2xl font-bold" :class="roleFilter === '' ? 'text-violet-700 dark:text-violet-300' : ''">
                        {{ loading ? '—' : (meta?.total ?? 0) }}
                    </p>
                    <div class="absolute right-3 top-3 flex h-7 w-7 items-center justify-center rounded-lg" :class="roleFilter === '' ? 'bg-violet-100 dark:bg-violet-900/40' : 'bg-muted'">
                        <User class="h-3.5 w-3.5" :class="roleFilter === '' ? 'text-violet-600' : 'text-muted-foreground'" />
                    </div>
                </button>

                <button
                    class="relative overflow-hidden rounded-xl border p-4 text-left transition-all hover:shadow-md"
                    :class="roleFilter === 'admin' ? 'border-rose-200 bg-rose-50 dark:border-rose-800 dark:bg-rose-900/20' : 'border-border bg-card hover:border-rose-200'"
                    @click="roleFilter = 'admin'; fetchUsers()"
                >
                    <p class="text-xs font-medium text-muted-foreground">Admins</p>
                    <p class="mt-1 text-2xl font-bold" :class="roleFilter === 'admin' ? 'text-rose-700 dark:text-rose-300' : ''">
                        {{ loading ? '—' : users.filter(u => u.role === 'admin').length }}
                    </p>
                    <div class="absolute right-3 top-3 flex h-7 w-7 items-center justify-center rounded-lg" :class="roleFilter === 'admin' ? 'bg-rose-100 dark:bg-rose-900/40' : 'bg-muted'">
                        <ShieldCheck class="h-3.5 w-3.5" :class="roleFilter === 'admin' ? 'text-rose-600' : 'text-muted-foreground'" />
                    </div>
                </button>

                <button
                    class="relative overflow-hidden rounded-xl border p-4 text-left transition-all hover:shadow-md"
                    :class="roleFilter === 'recruiter' ? 'border-indigo-200 bg-indigo-50 dark:border-indigo-800 dark:bg-indigo-900/20' : 'border-border bg-card hover:border-indigo-200'"
                    @click="roleFilter = 'recruiter'; fetchUsers()"
                >
                    <p class="text-xs font-medium text-muted-foreground">Recruiters</p>
                    <p class="mt-1 text-2xl font-bold" :class="roleFilter === 'recruiter' ? 'text-indigo-700 dark:text-indigo-300' : ''">
                        {{ loading ? '—' : users.filter(u => u.role === 'recruiter').length }}
                    </p>
                    <div class="absolute right-3 top-3 flex h-7 w-7 items-center justify-center rounded-lg" :class="roleFilter === 'recruiter' ? 'bg-indigo-100 dark:bg-indigo-900/40' : 'bg-muted'">
                        <Shield class="h-3.5 w-3.5" :class="roleFilter === 'recruiter' ? 'text-indigo-600' : 'text-muted-foreground'" />
                    </div>
                </button>
            </div>

            <!-- Table card -->
            <Card class="border-0 shadow-sm">
                <CardHeader class="border-b pb-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <CardTitle class="text-sm font-semibold">
                            {{ roleFilter === 'admin' ? 'Admins' : roleFilter === 'recruiter' ? 'Recruiters' : 'All Users' }}
                        </CardTitle>
                        <div class="relative w-full sm:w-72">
                            <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Search by name or email…"
                                class="w-full rounded-lg border border-border bg-background py-2 pl-9 pr-4 text-sm outline-none focus:border-violet-400 focus:ring-2 focus:ring-violet-100 dark:focus:ring-violet-900/40"
                                @keyup.enter="fetchUsers"
                                @input="search === '' && fetchUsers()"
                            />
                        </div>
                    </div>
                </CardHeader>

                <CardContent class="p-0">
                    <!-- Skeleton -->
                    <div v-if="loading" class="divide-y">
                        <div v-for="i in 6" :key="i" class="flex items-center gap-4 px-6 py-4 animate-pulse">
                            <div class="h-9 w-9 shrink-0 rounded-full bg-muted" />
                            <div class="flex-1 space-y-2">
                                <div class="h-3.5 w-40 rounded bg-muted" />
                                <div class="h-3 w-56 rounded bg-muted/70" />
                            </div>
                            <div class="h-5 w-16 rounded-full bg-muted" />
                            <div class="h-7 w-24 rounded-lg bg-muted/70" />
                        </div>
                    </div>

                    <!-- Empty -->
                    <div v-else-if="!users.length" class="flex flex-col items-center justify-center py-16 text-center">
                        <div class="mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-muted">
                            <User class="h-7 w-7 text-muted-foreground" />
                        </div>
                        <p class="text-sm font-medium">No users found</p>
                        <p class="mt-1 text-xs text-muted-foreground">Try adjusting your search or filter.</p>
                    </div>

                    <!-- Rows -->
                    <div v-else class="divide-y">
                        <div
                            v-for="u in users"
                            :key="u.id"
                            class="flex flex-col gap-2 px-6 py-4 transition-colors hover:bg-muted/30 sm:flex-row sm:items-center sm:gap-4"
                        >
                            <!-- Avatar -->
                            <div
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-sm font-bold text-white"
                                :class="getAvatarColor(u.name)"
                            >
                                {{ getInitials(u.name) }}
                            </div>

                            <!-- Name + email -->
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold">{{ u.name }}</p>
                                <p class="flex items-center gap-1 truncate text-xs text-muted-foreground">
                                    <Mail class="h-3 w-3 shrink-0" />{{ u.email }}
                                </p>
                            </div>

                            <!-- Joined date -->
                            <p class="hidden text-xs text-muted-foreground sm:block">
                                Joined {{ formatDate(u.created_at) }}
                            </p>

                            <!-- Role badge -->
                            <Badge
                                variant="outline"
                                class="w-fit rounded-full px-2.5 py-0.5 text-[11px] font-medium capitalize"
                                :class="u.role === 'admin'
                                    ? 'bg-rose-100 text-rose-700 border-rose-200 dark:bg-rose-900/30 dark:text-rose-300'
                                    : 'bg-indigo-100 text-indigo-700 border-indigo-200 dark:bg-indigo-900/30 dark:text-indigo-300'"
                            >
                                <ShieldCheck v-if="u.role === 'admin'" class="mr-1 h-3 w-3 inline-block" />
                                <Shield v-else class="mr-1 h-3 w-3 inline-block" />
                                {{ u.role_label }}
                            </Badge>

                            <!-- Toggle role button -->
                            <Button
                                variant="ghost"
                                size="sm"
                                class="h-8 shrink-0 gap-1.5 px-3 text-xs"
                                :class="u.role === 'admin'
                                    ? 'hover:bg-amber-50 hover:text-amber-700 dark:hover:bg-amber-900/20'
                                    : 'hover:bg-rose-50 hover:text-rose-700 dark:hover:bg-rose-900/20'"
                                :disabled="updatingRole === u.id"
                                @click="toggleRole(u)"
                            >
                                <ShieldOff v-if="u.role === 'admin'" class="h-3.5 w-3.5" />
                                <ShieldCheck v-else class="h-3.5 w-3.5" />
                                {{ u.role === 'admin' ? 'Make Recruiter' : 'Make Admin' }}
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
