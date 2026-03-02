<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowRight,
    Briefcase,
    Building2,
    CheckCircle,
    MapPin,
    Search,
    TrendingUp,
    UserCheck,
    UserPlus,
    Users,
} from 'lucide-vue-next';
import { ref } from 'vue';
import { dashboard, login, register } from '@/routes';

const props = defineProps<{
    canRegister: boolean;
    settings: Record<string, any>;
    jobs: Array<{
        uuid: string;
        title: string;
        location: string | null;
        employment_type: string;
        employment_type_label: string;
        deadline: string | null;
        applications_count: number;
        company: string | null;
    }>;
    stats: {
        total_jobs: number;
        total_applications: number;
        total_candidates: number;
        employment_types: number;
    };
}>();

const typeColors: Record<string, string> = {
    full_time: 'bg-blue-50 text-blue-700 ring-1 ring-blue-200',
    part_time: 'bg-amber-50 text-amber-700 ring-1 ring-amber-200',
    contract: 'bg-purple-50 text-purple-700 ring-1 ring-purple-200',
    remote: 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200',
};

const applyForm = ref({ name: '', email: '', type: '' });
const applySubmitting = ref(false);
const applySuccess = ref(false);

async function handleApply() {
    if (!applyForm.value.email) return;
    applySubmitting.value = true;
    await new Promise((r) => setTimeout(r, 800));
    applySuccess.value = true;
    applySubmitting.value = false;
}
</script>

<template>
    <Head title="Talentry — Find Your Dream Job" />

    <div class="min-h-screen bg-white font-sans antialiased">
        <!-- NAV -->
        <header class="sticky top-0 z-50 border-b border-slate-100 bg-white/95 backdrop-blur">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
                <a href="/" class="flex items-center gap-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-linear-to-br from-violet-600 to-indigo-600">
                        <Briefcase class="h-4 w-4 text-white" />
                    </div>
                    <span class="text-xl font-bold tracking-tight text-slate-900">Talentry</span>
                </a>
                <nav class="hidden items-center gap-6 text-sm font-medium text-slate-600 md:flex">
                    <a href="/careers" class="transition hover:text-violet-600">Browse Jobs</a>
                    <a href="#how-it-works" class="transition hover:text-violet-600">How It Works</a>
                    <a href="#stats" class="transition hover:text-violet-600">About</a>
                </nav>
                <div class="flex items-center gap-3">
                    <template v-if="$page.props.auth?.user">
                        <Link
                            :href="dashboard()"
                            class="rounded-lg bg-violet-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-violet-700"
                        >
                            Dashboard
                        </Link>
                    </template>
                    <template v-else>
                        <Link :href="login()" class="text-sm font-medium text-slate-600 transition hover:text-violet-600">Log in</Link>
                        <Link
                            v-if="canRegister"
                            :href="register()"
                            class="rounded-lg bg-violet-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-violet-700"
                        >
                            Get Started
                        </Link>
                    </template>
                </div>
            </div>
        </header>

        <!-- HERO -->
        <section class="relative overflow-hidden bg-linear-to-br from-slate-950 via-violet-950 to-indigo-950 pb-28 pt-24 text-white">
            <div class="pointer-events-none absolute inset-0">
                <div class="absolute left-1/2 top-1/3 h-96 w-96 -translate-x-1/2 -translate-y-1/2 rounded-full bg-violet-600/20 blur-3xl"></div>
                <div class="absolute right-20 top-10 h-64 w-64 rounded-full bg-indigo-500/10 blur-3xl"></div>
            </div>
            <div class="relative mx-auto max-w-4xl px-6 text-center">
                <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-violet-500/30 bg-violet-500/10 px-4 py-1.5 text-sm text-violet-300">
                    <span class="h-2 w-2 animate-pulse rounded-full bg-violet-400"></span>
                    {{ stats.total_jobs }} jobs available right now
                </div>
                <h1 class="mb-6 text-5xl font-extrabold leading-tight tracking-tight lg:text-6xl">
                    {{ settings.hero_title ?? 'Find Your Dream Job Today' }}
                </h1>
                <p class="mx-auto mb-10 max-w-2xl text-lg leading-relaxed text-slate-300">
                    {{ settings.hero_subtitle ?? 'Connecting top talent with world-class companies.' }}
                </p>
                <!-- Search bar -->
                <div class="mx-auto mb-8 flex max-w-xl overflow-hidden rounded-2xl bg-white shadow-2xl shadow-black/30">
                    <div class="flex flex-1 items-center gap-3 px-5">
                        <Search class="h-5 w-5 shrink-0 text-slate-400" />
                        <input
                            type="text"
                            placeholder="Job title, keyword or company…"
                            class="w-full py-4 text-sm text-slate-800 outline-none placeholder:text-slate-400"
                        />
                    </div>
                    <a
                        href="/careers"
                        class="m-2 flex items-center gap-2 rounded-xl bg-linear-to-br from-violet-600 to-indigo-600 px-5 py-3 text-sm font-semibold text-white transition hover:opacity-90"
                    >
                        Search <ArrowRight class="h-4 w-4" />
                    </a>
                </div>
                <!-- CTA buttons -->
                <div class="flex flex-wrap justify-center gap-3">
                    <a
                        href="/careers"
                        class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-6 py-3 text-sm font-semibold text-white ring-1 ring-white/20 backdrop-blur transition hover:bg-white/20"
                    >
                        <Briefcase class="h-4 w-4" />
                        {{ settings.hero_cta_primary ?? 'Browse All Jobs' }}
                    </a>
                    <Link
                        v-if="canRegister"
                        :href="register()"
                        class="inline-flex items-center gap-2 rounded-xl bg-violet-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-violet-700"
                    >
                        {{ settings.hero_cta_secondary ?? 'Get Started Free' }}
                        <ArrowRight class="h-4 w-4" />
                    </Link>
                </div>
            </div>
        </section>

        <!-- STATS -->
        <section id="stats" class="border-b border-slate-100 bg-slate-50 py-16">
            <div class="mx-auto max-w-5xl px-6">
                <div class="mb-10 text-center">
                    <h2 class="text-2xl font-bold text-slate-900">{{ settings.stats_section_title ?? 'Trusted by Thousands' }}</h2>
                    <p class="mt-1 text-sm text-slate-500">{{ settings.stats_section_subtitle ?? 'Real numbers. Real opportunities.' }}</p>
                </div>
                <div class="grid grid-cols-2 gap-5 md:grid-cols-4">
                    <div class="flex flex-col items-center rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
                        <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-linear-to-br from-violet-500 to-indigo-600">
                            <Briefcase class="h-6 w-6 text-white" />
                        </div>
                        <p class="text-3xl font-extrabold tabular-nums text-slate-900">{{ stats.total_jobs }}+</p>
                        <p class="mt-1 text-xs font-medium text-slate-500">Open Positions</p>
                    </div>
                    <div class="flex flex-col items-center rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
                        <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-linear-to-br from-emerald-500 to-teal-600">
                            <TrendingUp class="h-6 w-6 text-white" />
                        </div>
                        <p class="text-3xl font-extrabold tabular-nums text-slate-900">{{ stats.total_applications }}+</p>
                        <p class="mt-1 text-xs font-medium text-slate-500">Applications Sent</p>
                    </div>
                    <div class="flex flex-col items-center rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
                        <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-linear-to-br from-amber-500 to-orange-600">
                            <Users class="h-6 w-6 text-white" />
                        </div>
                        <p class="text-3xl font-extrabold tabular-nums text-slate-900">{{ stats.total_candidates }}+</p>
                        <p class="mt-1 text-xs font-medium text-slate-500">Registered Candidates</p>
                    </div>
                    <div class="flex flex-col items-center rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
                        <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-linear-to-br from-rose-500 to-pink-600">
                            <Building2 class="h-6 w-6 text-white" />
                        </div>
                        <p class="text-3xl font-extrabold tabular-nums text-slate-900">{{ stats.employment_types }}</p>
                        <p class="mt-1 text-xs font-medium text-slate-500">Job Categories</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- JOBS -->
        <section id="jobs" class="py-20">
            <div class="mx-auto max-w-5xl px-6">
                <div class="mb-10 flex items-end justify-between">
                    <div>
                        <h2 class="text-3xl font-bold text-slate-900">{{ settings.jobs_section_title ?? 'Latest Opportunities' }}</h2>
                        <p class="mt-1 text-sm text-slate-500">{{ settings.jobs_section_subtitle ?? 'Hand-picked roles from leading companies.' }}</p>
                    </div>
                    <a href="/careers" class="hidden items-center gap-1 text-sm font-semibold text-violet-600 transition hover:text-violet-700 md:flex">
                        View all <ArrowRight class="h-4 w-4" />
                    </a>
                </div>

                <div v-if="!jobs.length" class="py-20 text-center text-slate-400">
                    <Briefcase class="mx-auto mb-3 h-12 w-12 opacity-30" />
                    <p class="text-lg font-medium">No open positions right now.</p>
                    <p class="mt-1 text-sm">Check back soon!</p>
                </div>

                <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <a
                        v-for="job in jobs"
                        :key="job.uuid"
                        :href="`/careers/${job.uuid}`"
                        class="group flex flex-col rounded-2xl border border-slate-100 bg-white p-5 shadow-sm transition hover:border-violet-200 hover:shadow-md"
                    >
                        <div class="mb-3 flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-linear-to-br from-violet-500 to-indigo-600">
                                <Briefcase class="h-5 w-5 text-white" />
                            </div>
                            <p class="truncate text-xs font-medium text-slate-500">{{ job.company ?? 'Independent' }}</p>
                        </div>
                        <h3 class="mb-2 flex-1 text-base font-semibold leading-snug text-slate-900 group-hover:text-violet-600">{{ job.title }}</h3>
                        <div class="flex items-center gap-1 text-xs text-slate-500">
                            <MapPin v-if="job.location" class="h-3 w-3 shrink-0" />
                            <span v-if="job.location">{{ job.location }}</span>
                        </div>
                        <div class="mt-3 flex items-center justify-between">
                            <span
                                class="rounded-full px-2.5 py-0.5 text-xs font-medium"
                                :class="typeColors[job.employment_type] ?? 'bg-slate-100 text-slate-600'"
                            >
                                {{ job.employment_type_label }}
                            </span>
                            <span v-if="job.deadline" class="text-xs text-slate-400">Due {{ job.deadline }}</span>
                        </div>
                    </a>
                </div>

                <div class="mt-10 text-center md:hidden">
                    <a href="/careers" class="inline-flex items-center gap-2 rounded-xl border border-violet-200 px-6 py-3 text-sm font-semibold text-violet-600 transition hover:bg-violet-50">
                        View all jobs <ArrowRight class="h-4 w-4" />
                    </a>
                </div>
            </div>
        </section>

        <!-- HOW IT WORKS -->
        <section id="how-it-works" class="bg-slate-50 py-20">
            <div class="mx-auto max-w-5xl px-6">
                <div class="mb-14 text-center">
                    <h2 class="text-3xl font-bold text-slate-900">{{ settings.how_it_works_title ?? 'How It Works' }}</h2>
                    <p class="mt-2 text-slate-500">{{ settings.how_it_works_subtitle ?? 'Three simple steps to your next role.' }}</p>
                </div>
                <div class="grid gap-8 md:grid-cols-3">
                    <div
                        v-for="(step, i) in settings.how_it_works_steps ?? []"
                        :key="i"
                        class="relative flex flex-col items-center rounded-2xl bg-white p-8 text-center shadow-sm ring-1 ring-slate-100"
                    >
                        <div class="absolute -top-4 left-6 flex h-8 w-8 items-center justify-center rounded-full bg-violet-600 text-sm font-bold text-white shadow">
                            {{ i + 1 }}
                        </div>
                        <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-linear-to-br from-violet-600 to-indigo-600 text-white shadow-md">
                            <UserPlus v-if="step.icon === 'UserPlus'" class="h-7 w-7" />
                            <Search v-else-if="step.icon === 'Search'" class="h-7 w-7" />
                            <CheckCircle v-else class="h-7 w-7" />
                        </div>
                        <h3 class="mb-2 text-lg font-semibold text-slate-900">{{ step.title }}</h3>
                        <p class="text-sm leading-relaxed text-slate-500">{{ step.description }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- APPLY FORM -->
        <section id="apply" class="py-20">
            <div class="mx-auto max-w-3xl px-6">
                <div class="overflow-hidden rounded-3xl bg-linear-to-br from-violet-600 to-indigo-700 shadow-2xl shadow-violet-300/40">
                    <div class="p-10 text-center text-white md:p-14">
                        <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-white/20">
                            <UserCheck class="h-7 w-7" />
                        </div>
                        <h2 class="mb-2 text-3xl font-extrabold">{{ settings.apply_section_title ?? 'Jump-Start Your Career' }}</h2>
                        <p class="mb-8 text-violet-200">{{ settings.apply_section_subtitle ?? 'Submit a quick application and let top employers discover you.' }}</p>

                        <div v-if="applySuccess" class="rounded-2xl bg-white/15 p-6">
                            <CheckCircle class="mx-auto mb-2 h-10 w-10 text-emerald-300" />
                            <p class="text-lg font-semibold">You're on the list!</p>
                            <p class="mt-1 text-sm text-violet-200">We'll notify you about matching opportunities.</p>
                        </div>

                        <form v-else class="space-y-3 text-left" @submit.prevent="handleApply">
                            <div class="grid gap-3 sm:grid-cols-2">
                                <input
                                    v-model="applyForm.name"
                                    type="text"
                                    placeholder="Your full name"
                                    class="w-full rounded-xl bg-white/15 px-4 py-3 text-sm text-white placeholder:text-violet-300 outline-none ring-1 ring-white/20 transition focus:bg-white/20 focus:ring-white/40"
                                />
                                <input
                                    v-model="applyForm.email"
                                    type="email"
                                    placeholder="Email address"
                                    required
                                    class="w-full rounded-xl bg-white/15 px-4 py-3 text-sm text-white placeholder:text-violet-300 outline-none ring-1 ring-white/20 transition focus:bg-white/20 focus:ring-white/40"
                                />
                            </div>
                            <select
                                v-model="applyForm.type"
                                class="w-full rounded-xl bg-white/15 px-4 py-3 text-sm text-white outline-none ring-1 ring-white/20 transition focus:bg-white/20 focus:ring-white/40"
                            >
                                <option value="" class="text-slate-900">Preferred job type</option>
                                <option value="full_time" class="text-slate-900">Full-Time</option>
                                <option value="part_time" class="text-slate-900">Part-Time</option>
                                <option value="contract" class="text-slate-900">Contract</option>
                                <option value="remote" class="text-slate-900">Remote</option>
                            </select>
                            <button
                                type="submit"
                                :disabled="applySubmitting"
                                class="flex w-full items-center justify-center gap-2 rounded-xl bg-white py-3.5 text-sm font-bold text-violet-700 transition hover:bg-violet-50 disabled:opacity-70"
                            >
                                <span v-if="applySubmitting">Submitting…</span>
                                <template v-else>
                                    Get Notified of Matches
                                    <ArrowRight class="h-4 w-4" />
                                </template>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA BAND -->
        <section class="bg-slate-950 py-16 text-center text-white">
            <div class="mx-auto max-w-2xl px-6">
                <h2 class="mb-3 text-3xl font-extrabold">{{ settings.cta_title ?? 'Ready to Take the Next Step?' }}</h2>
                <p class="mb-8 text-slate-400">{{ settings.cta_subtitle ?? 'Join thousands of professionals who found their dream jobs through Talentry.' }}</p>
                <Link
                    v-if="canRegister"
                    :href="register()"
                    class="inline-flex items-center gap-2 rounded-xl bg-linear-to-br from-violet-600 to-indigo-600 px-8 py-4 text-sm font-bold text-white transition hover:opacity-90"
                >
                    {{ settings.cta_button ?? 'Get Started Free' }}
                    <ArrowRight class="h-4 w-4" />
                </Link>
                <Link
                    v-else
                    :href="login()"
                    class="inline-flex items-center gap-2 rounded-xl bg-linear-to-br from-violet-600 to-indigo-600 px-8 py-4 text-sm font-bold text-white transition hover:opacity-90"
                >
                    Log In <ArrowRight class="h-4 w-4" />
                </Link>
            </div>
        </section>

        <!-- FOOTER -->
        <footer class="border-t border-slate-800 bg-slate-950 px-6 py-12 text-sm text-slate-500">
            <div class="mx-auto max-w-5xl">
                <div class="flex flex-col items-center justify-between gap-6 md:flex-row">
                    <div class="flex items-center gap-2">
                        <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-linear-to-br from-violet-600 to-indigo-600">
                            <Briefcase class="h-3.5 w-3.5 text-white" />
                        </div>
                        <span class="font-semibold text-white">Talentry</span>
                    </div>
                    <p class="text-center text-slate-500">{{ settings.footer_tagline ?? 'Connecting talent with opportunity, one job at a time.' }}</p>
                    <nav class="flex flex-wrap justify-center gap-4">
                        <a
                            v-for="link in settings.footer_links ?? []"
                            :key="link.href"
                            :href="link.href"
                            class="transition hover:text-white"
                        >
                            {{ link.label }}
                        </a>
                    </nav>
                </div>
                <div class="mt-8 border-t border-slate-800 pt-6 text-center text-xs text-slate-600">
                    &copy; {{ new Date().getFullYear() }} Talentry. All rights reserved.
                </div>
            </div>
        </footer>
    </div>
</template>
