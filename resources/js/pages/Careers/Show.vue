<script setup lang="ts">
import api from '@/lib/api';
import { onMounted, ref } from 'vue';

const props = defineProps<{ uuid: string }>();

const job = ref<any>(null);
const loading = ref(true);
const form = ref({ name: '', email: '', phone: '', cover_letter: '', resume: null as File | null });
const submitting = ref(false);
const submitted = ref(false);
const errors = ref<Record<string, string>>({});
const uploadLabel = ref('Click to upload your resume (PDF or Word, max 5MB)');

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
</script>

<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-950">
        <header class="border-b border-gray-100 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="mx-auto max-w-3xl px-6 py-5">
                <a href="/careers" class="text-sm text-blue-600 hover:underline dark:text-blue-400">← Back to Careers</a>
            </div>
        </header>

        <main class="mx-auto max-w-3xl px-6 py-10">
            <div v-if="loading" class="animate-pulse space-y-4">
                <div class="h-8 w-64 rounded bg-gray-200 dark:bg-gray-700"></div>
                <div class="h-4 w-48 rounded bg-gray-100 dark:bg-gray-700"></div>
            </div>

            <template v-else-if="job">
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ job.title }}</h1>
                    <p class="mt-2 text-sm text-gray-500">📍 {{ job.location }} · {{ job.employment_type_label }}</p>
                    <div v-if="job.deadline" class="mt-1 text-xs text-gray-400">Deadline: {{ job.deadline }}</div>
                </div>

                <!-- Description -->
                <div class="prose dark:prose-invert mb-10 max-w-none rounded-xl border border-gray-100 bg-white p-6 text-sm dark:border-gray-700 dark:bg-gray-800">
                    <p class="whitespace-pre-wrap text-gray-700 dark:text-gray-300">{{ job.description }}</p>
                </div>

                <!-- Success -->
                <div
                    v-if="submitted"
                    class="rounded-xl border border-green-200 bg-green-50 p-8 text-center dark:border-green-700 dark:bg-green-900/30"
                >
                    <p class="text-2xl">🎉</p>
                    <h2 class="mt-2 text-xl font-semibold text-green-800 dark:text-green-300">Application Submitted!</h2>
                    <p class="mt-1 text-sm text-green-600 dark:text-green-400">Thank you for applying. We'll review your application and be in touch soon.</p>
                </div>

                <!-- Apply Form -->
                <div v-else class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <h2 class="mb-6 text-xl font-semibold text-gray-900 dark:text-white">Apply for this position</h2>
                    <form class="space-y-5" @submit.prevent="apply">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Full Name *</label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    required
                                    class="w-full rounded-lg border px-3 py-2 text-sm focus:border-blue-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                    :class="errors.name ? 'border-red-400' : 'border-gray-200'"
                                />
                                <p v-if="errors.name" class="mt-1 text-xs text-red-500">{{ errors.name }}</p>
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Email *</label>
                                <input
                                    v-model="form.email"
                                    type="email"
                                    required
                                    class="w-full rounded-lg border px-3 py-2 text-sm focus:border-blue-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                    :class="errors.email ? 'border-red-400' : 'border-gray-200'"
                                />
                                <p v-if="errors.email" class="mt-1 text-xs text-red-500">{{ errors.email }}</p>
                            </div>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                            <input
                                v-model="form.phone"
                                type="tel"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                            />
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Cover Letter</label>
                            <textarea
                                v-model="form.cover_letter"
                                rows="5"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                placeholder="Tell us why you're a great fit..."
                            ></textarea>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Resume *</label>
                            <label
                                class="flex cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed px-6 py-8 transition hover:bg-gray-50 dark:hover:bg-gray-700/30"
                                :class="errors.resume ? 'border-red-400' : 'border-gray-200 dark:border-gray-600'"
                            >
                                <span class="mb-1 text-2xl">📎</span>
                                <span class="text-center text-sm text-gray-500 dark:text-gray-400">{{ uploadLabel }}</span>
                                <input type="file" class="hidden" accept=".pdf,.doc,.docx" @change="onFileChange" />
                            </label>
                            <p v-if="errors.resume" class="mt-1 text-xs text-red-500">{{ errors.resume }}</p>
                        </div>
                        <button
                            type="submit"
                            :disabled="submitting || !form.resume"
                            class="w-full rounded-lg bg-blue-600 py-3 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:opacity-50"
                        >
                            {{ submitting ? 'Submitting...' : 'Submit Application' }}
                        </button>
                    </form>
                </div>
            </template>
        </main>
    </div>
</template>
