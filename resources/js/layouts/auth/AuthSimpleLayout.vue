<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Briefcase } from 'lucide-vue-next';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="flex min-h-svh font-sans antialiased">
        <!-- Left brand panel (desktop only) -->
        <div class="hidden w-1/2 flex-col justify-between bg-linear-to-br from-slate-950 via-violet-950 to-indigo-950 p-12 text-white lg:flex">
            <Link :href="home()" class="flex items-center gap-2.5">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/15 ring-1 ring-white/20">
                    <Briefcase class="h-5 w-5" />
                </div>
                <span class="text-xl font-bold tracking-tight">Talentry</span>
            </Link>
            <div>
                <blockquote class="mb-6 text-xl font-semibold leading-snug text-white/90">
                    “Landing my dream role was effortless with Talentry — the best job platform I’ve used.”
                </blockquote>
                <div class="flex items-center gap-3">
                    <div class="h-9 w-9 rounded-full bg-violet-400/30 ring-1 ring-violet-400/40"></div>
                    <div>
                        <p class="text-sm font-semibold">Sarah Johnson</p>
                        <p class="text-xs text-slate-400">Senior Engineer, hired via Talentry</p>
                    </div>
                </div>
            </div>
            <p class="text-xs text-slate-500">&copy; {{ new Date().getFullYear() }} Talentry. All rights reserved.</p>
        </div>

        <!-- Right form panel -->
        <div class="flex flex-1 flex-col items-center justify-center px-6 py-12">
            <!-- Mobile logo -->
            <Link :href="home()" class="mb-8 flex items-center gap-2 lg:hidden">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-linear-to-br from-violet-600 to-indigo-600">
                    <Briefcase class="h-4 w-4 text-white" />
                </div>
                <span class="text-xl font-bold tracking-tight text-slate-900">Talentry</span>
            </Link>

            <div class="w-full max-w-sm">
                <div class="mb-8 text-center">
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ title }}</h1>
                    <p class="mt-1.5 text-sm text-slate-500">{{ description }}</p>
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>
