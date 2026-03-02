<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Briefcase, Star } from 'lucide-vue-next';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="flex min-h-svh font-sans antialiased">
        <!-- Left brand panel (desktop only) -->
        <div
            class="relative hidden w-[52%] overflow-hidden bg-linear-to-br from-slate-950 via-violet-950 to-indigo-950 lg:flex lg:flex-col lg:justify-between lg:p-14"
        >
            <!-- Decorative orbs -->
            <div class="absolute -top-40 -left-40 h-[28rem] w-[28rem] rounded-full bg-violet-600/25 blur-3xl"></div>
            <div class="absolute top-1/2 -right-28 h-80 w-80 -translate-y-1/2 rounded-full bg-indigo-500/20 blur-3xl"></div>
            <div class="absolute -bottom-28 left-1/3 h-72 w-72 rounded-full bg-purple-700/20 blur-3xl"></div>
            <!-- Dot grid -->
            <div
                class="absolute inset-0 opacity-[0.04]"
                style="background-image: radial-gradient(circle, #fff 1px, transparent 1px); background-size: 28px 28px"
            ></div>

            <!-- Logo -->
            <div class="relative z-10">
                <Link :href="home()" class="inline-flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/10 ring-1 ring-white/20 backdrop-blur-sm">
                        <Briefcase class="h-5 w-5 text-white" />
                    </div>
                    <span class="text-xl font-bold tracking-tight text-white">Talentry</span>
                </Link>
            </div>
            <!-- Middle content -->
            <div class="relative z-10 space-y-8">
                <div>
                    <h2 class="mb-3 text-[2rem] font-extrabold leading-tight tracking-tight text-white">
                        Your next career<br />move starts here.
                    </h2>
                    <p class="text-[0.9375rem] leading-relaxed text-slate-400">
                        Connect with top companies and land your dream role faster than ever before.
                    </p>
                </div>

                <!-- Stats grid -->
                <div class="grid grid-cols-3 gap-3">
                    <div class="rounded-2xl bg-white/5 p-4 ring-1 ring-white/10 backdrop-blur-sm">
                        <p class="text-2xl font-bold text-white">10k+</p>
                        <p class="mt-0.5 text-xs text-slate-400">Active Jobs</p>
                    </div>
                    <div class="rounded-2xl bg-white/5 p-4 ring-1 ring-white/10 backdrop-blur-sm">
                        <p class="text-2xl font-bold text-white">500+</p>
                        <p class="mt-0.5 text-xs text-slate-400">Companies</p>
                    </div>
                    <div class="rounded-2xl bg-white/5 p-4 ring-1 ring-white/10 backdrop-blur-sm">
                        <p class="text-2xl font-bold text-white">50k+</p>
                        <p class="mt-0.5 text-xs text-slate-400">Placed</p>
                    </div>
                </div>
                <!-- Testimonial card -->
                <div class="rounded-2xl bg-white/5 p-6 ring-1 ring-white/10 backdrop-blur-sm">
                    <div class="mb-3 flex gap-0.5">
                        <Star v-for="i in 5" :key="i" class="h-4 w-4 fill-amber-400 text-amber-400" />
                    </div>
                    <blockquote class="mb-5 text-[0.9375rem] font-medium leading-relaxed text-white/90">
                    “Landing my dream role was effortless with Talentry — the best job platform I’ve used.”
                    </blockquote>
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-linear-to-br from-violet-400 to-indigo-400 text-sm font-bold text-white ring-1 ring-white/20">
                            SJ
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-white">Sarah Johnson</p>
                            <p class="text-xs text-slate-400">Senior Engineer · Hired via Talentry</p>
                        </div>
                    </div>
                </div>
            </div>
            <p class="relative z-10 text-xs text-slate-600">&copy; {{ new Date().getFullYear() }} Talentry. All rights reserved.</p>
        </div>

        <!-- Right form panel -->
        <div class="relative flex flex-1 flex-col items-center justify-center bg-slate-50 px-6 py-12">
            <!-- Subtle gradient wash -->
            <div class="pointer-events-none absolute inset-0 bg-linear-to-br from-violet-50/70 via-white to-indigo-50/50"></div>

            <!-- Mobile logo -->
            <Link :href="home()" class="relative mb-8 flex items-center gap-2.5 lg:hidden">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-linear-to-br from-violet-600 to-indigo-600 shadow-lg shadow-violet-500/30">
                    <Briefcase class="h-5 w-5 text-white" />
                </div>
                <span class="text-xl font-bold tracking-tight text-slate-900">Talentry</span>
            </Link>

            <!-- Form card -->
            <div class="relative w-full max-w-[22rem]">
                <div class="rounded-2xl bg-white px-8 py-9 shadow-2xl shadow-slate-300/50 ring-1 ring-slate-200/70">
                    <div class="mb-7 text-center">
                        <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ title }}</h1>
                        <p class="mt-1.5 text-sm text-slate-500">{{ description }}</p>
                    </div>
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>
