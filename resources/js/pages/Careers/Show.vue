<script setup lang="ts">
import { ArrowLeft, Briefcase, Calendar, CheckCircle, Loader2, MapPin, Paperclip } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';
import api from '@/lib/api';

const props = defineProps<{ uuid: string }>();

const job = ref<any>(null);
const loading = ref(true);
const form = ref({ name: '', email: '', phone: '', cover_letter: '', resume: null as File | null });
const submitting = ref(false);
const submitted = ref(false);
const errors = ref<Record<string, string>>({});
const uploadLabel = ref('Click to upload resume (PDF or Word, max 5 MB)');

onMounted(async () => {
    const { data } = await api.get(`/public/jobs/${props.uuid}`);
    job.value = data.data;
    loading.value = false;
});

function onFileChange(e: Event) {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (file) {
        form.value.resume = file;
        uploadLabel.value = file.name;
    }
}

async function apply() {
    if (!form.value.resume) return;
    submitting.value = true;
    errors.value = {};
    try {
        const formData = new FormData();
        formData.append('name', form.value.name);
        formData.append('email', form.value.email);
        if (form.value.phone) formData.append('phone', form.value.phone);
        if (form.value.cover_letter) formData.append('cover_letter', form.value.cover_letter);
        formData.append('resume', form.value.resume);
        await api.post(`/public/jobs/${props.uuid}/apply`, formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        });
        submitted.value = true;
    } catch (e: any) {
        if (e.response?.data?.errors) {
            for (const [key, msgs] of Object.entries<string[]>(e.response.data.errors)) {
                errors.value[key] = msgs[0];
            }
        }
    } finally {
        submitting.value = false;
    }
}

const typeColors: Record<string, string> = {
    full_time: 'bg-blue-50 text-blue-700 ring-1 ring-blue-200',
    part_time: 'bg-amber-50 text-amber-700 ring-1 ring-amber-200',
    contract: 'bg-purple-50 text-purple-700 ring-1 ring-purple-200',
    remote: 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200',
};
</script>

<template>
    <div class="min-h-screen bg-white font-sans antialiased">
        <!-- NAV -->
        <header class="sticky top-0 z-50 border-b border-slate-100 bg-white/95 backdrop-blur">
            <div class="mx-auto flex max-w-4xl items-center justify-between px-6 py-4">
                <a href="/" class="flex items-center gap-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-linear-to-br from-violet-600 to-indigo-600">
                        <Briefcase class="h-4 w-4 text-white" />
                    </div>
                    <span class="text-xl font-bold tracking-tight text-slate-900">Talentry</span>
                </a>
                <a href="/careers" class="flex items-center gap-1.5 text-sm font-medium text-slate-500 transition hover:text-violet-600">
                    <ArrowLeft class="h-4 w-4" /> All Jobs
                </a>
            </div>
        </header>

        <!-- LOADING -->
        <div v-if="loading" class="mx-auto max-w-4xl space-y-4 px-6 py-14 animate-pulse">
            <div class="h-10 w-3/4 rounded-xl bg-slate-100"></div>
            <div class="h-5 w-48 rounded bg-slate-100"></div>
            <div class="mt-6 h-64 rounded-2xl bg-slate-100"></div>
        </div>

        <template v-else-if="job">
            <!-- JOB HERO -->
            <section class="bg-linear-to-br from-slate-950 via-violet-950 to-indigo-950 pb-12 pt-12 text-white">
                <div class="mx-auto max-w-4xl px-6">
                    <div class="flex items-start gap-5">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white/15 ring-1 ring-white/20">
                            <Briefcase class="h-7 w-7" />
                        </div>
                        <div>
                            <h1 class="text-3xl font-extrabold tracking-tight lg:text-4xl">{{ job.title }}</h1>
                            <div class="mt-2 flex flex-wrap items-center gap-4 text-sm text-slate-300">
                                <span v-if="job.location" class="flex items-center gap-1.5">
                                    <MapPin class="h-4 w-4" />{{ job.location }}
                                </span>
                                <span v-if="job.deadline" class="flex items-center gap-1.5">
                                    <Calendar class="h-4 w-4" />Apply by {{ job.deadline }}
                                </span>
                                <span
                                    v-if="job.employment_type"
                                    class="rounded-full px-3 py-0.5 text-xs font-medium"
                                    :class="typeColors[job.employment_type] ?? 'bg-white/10 text-white'"
                                >
                                    {{ job.employment_type_label }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <main class="mx-auto max-w-4xl px-6 py-10">
                <div class="grid gap-8 lg:grid-cols-5">
                    <!-- Description -->
                    <div class="lg:col-span-3">
                        <h2 class="mb-4 text-lg font-semibold text-slate-900">Job Description</h2>
                        <div class="rounded-2xl border border-slate-100 bg-slate-50 p-6">
                            <p class="whitespace-pre-wrap text-sm leading-relaxed text-slate-700">{{ job.description }}</p>
                        </div>
                    </div>

                    <!-- Apply Form / Success -->
                    <div class="lg:col-span-2">
                        <!-- Success -->
                        <div v-if="submitted" class="flex flex-col items-center rounded-2xl bg-linear-to-br from-violet-600 to-indigo-600 p-8 text-center text-white shadow-lg">
                            <CheckCircle class="mb-3 h-12 w-12" />
                            <h2 class="text-xl font-bold">Application Submitted!</h2>
                            <p class="mt-2 text-sm text-violet-200">Thank you for applying. We’ll review your submission and be in touch soon.</p>
                            <a href="/careers" class="mt-6 rounded-xl bg-white/20 px-5 py-2.5 text-sm font-semibold ring-1 ring-white/30 transition hover:bg-white/30">
                                Browse More Jobs
                            </a>
                        </div>

                        <!-- Form -->
                        <div v-else class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                            <h2 class="mb-5 text-lg font-semibold text-slate-900">Apply Now</h2>
                            <form class="space-y-4" @submit.prevent="apply">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-slate-700">Full Name *</label>
                                    <input
                                        v-model="form.name"
                                        type="text"
                                        required
                                        placeholder="Jane Doe"
                                        class="w-full rounded-xl border px-3.5 py-2.5 text-sm shadow-sm outline-none transition focus:ring-2 focus:ring-violet-500"
                                        :class="errors.name ? 'border-red-400' : 'border-slate-200'"
                                    />
                                    <p v-if="errors.name" class="mt-1 text-xs text-red-500">{{ errors.name }}</p>
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-slate-700">Email *</label>
                                    <input
                                        v-model="form.email"
                                        type="email"
                                        required
                                        placeholder="jane@example.com"
                                        class="w-full rounded-xl border px-3.5 py-2.5 text-sm shadow-sm outline-none transition focus:ring-2 focus:ring-violet-500"
                                        :class="errors.email ? 'border-red-400' : 'border-slate-200'"
                                    />
                                    <p v-if="errors.email" class="mt-1 text-xs text-red-500">{{ errors.email }}</p>
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-slate-700">Phone</label>
                                    <input
                                        v-model="form.phone"
                                        type="tel"
                                        placeholder="+1 555 000 000"
                                        class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm shadow-sm outline-none transition focus:ring-2 focus:ring-violet-500"
                                    />
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-slate-700">Cover Letter</label>
                                    <textarea
                                        v-model="form.cover_letter"
                                        rows="4"
                                        placeholder="Tell us why you’re a great fit…"
                                        class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm shadow-sm outline-none transition focus:ring-2 focus:ring-violet-500"
                                    ></textarea>
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-slate-700">Resume *</label>
                                    <label
                                        class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed py-7 transition hover:bg-violet-50"
                                        :class="errors.resume ? 'border-red-400' : 'border-slate-200 hover:border-violet-400'"
                                    >
                                        <Paperclip class="h-6 w-6 text-slate-400" />
                                        <span class="text-center text-xs text-slate-500">{{ uploadLabel }}</span>
                                        <input type="file" class="hidden" accept=".pdf,.doc,.docx" @change="onFileChange" />
                                    </label>
                                    <p v-if="errors.resume" class="mt-1 text-xs text-red-500">{{ errors.resume }}</p>
                                </div>
                                <button
                                    type="submit"
                                    :disabled="submitting || !form.resume"
                                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-linear-to-br from-violet-600 to-indigo-600 py-3 text-sm font-bold text-white shadow transition hover:opacity-90 disabled:opacity-60"
                                >
                                    <Loader2 v-if="submitting" class="h-4 w-4 animate-spin" />
                                    {{ submitting ? 'Submitting…' : 'Submit Application' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
        </template>

        <!-- FOOTER -->
        <footer class="border-t border-slate-100 py-8 text-center text-xs text-slate-400">
            &copy; {{ new Date().getFullYear() }} Talentry
        </footer>
    </div>
</template>
