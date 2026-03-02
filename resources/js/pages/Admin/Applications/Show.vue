<script setup lang="ts">
import { onMounted, ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { useApplicationStore } from '@/stores/application.store';

const props = defineProps<{ uuid: string }>();
const applicationStore = useApplicationStore();
const newNote = ref('');
const statusOptions = ['applied', 'shortlisted', 'interview', 'rejected', 'hired'];
const updatingStatus = ref(false);
const addingNote = ref(false);

const statusColors: Record<string, string> = {
    applied: 'bg-blue-100 text-blue-700',
    shortlisted: 'bg-yellow-100 text-yellow-700',
    interview: 'bg-purple-100 text-purple-700',
    rejected: 'bg-red-100 text-red-700',
    hired: 'bg-green-100 text-green-700',
};

onMounted(() => applicationStore.fetchApplication(props.uuid));

async function changeStatus(status: string) {
    updatingStatus.value = true;
    await applicationStore.updateStatus(props.uuid, status);
    updatingStatus.value = false;
}

async function submitNote() {
    if (!newNote.value.trim()) return;
    addingNote.value = true;
    await applicationStore.addNote(props.uuid, newNote.value.trim());
    newNote.value = '';
    addingNote.value = false;
}
</script>

<template>
    <AppLayout
        :breadcrumbs="[
            { title: 'Applications', href: '/admin/applications' },
            { title: 'Review', href: `/admin/applications/${uuid}` },
        ]"
    >
        <div class="mx-auto max-w-4xl space-y-6 p-6">
            <div v-if="applicationStore.loading" class="animate-pulse">
                <div class="mb-4 h-6 w-48 rounded bg-gray-200 dark:bg-gray-700"></div>
                <div class="h-4 w-32 rounded bg-gray-100 dark:bg-gray-700"></div>
            </div>
            <template v-else-if="applicationStore.application">
                <div class="flex items-start justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ applicationStore.application.candidate?.name }}
                        </h1>
                        <p class="mt-1 text-sm text-gray-500">
                            Applied for: <strong>{{ applicationStore.application.job_posting?.title }}</strong>
                        </p>
                    </div>
                    <span
                        class="rounded-full px-3 py-1 text-sm font-medium"
                        :class="statusColors[applicationStore.application.status] ?? 'bg-gray-100 text-gray-600'"
                    >
                        {{ applicationStore.application.status_label }}
                    </span>
                </div>

                <div class="grid gap-6 lg:grid-cols-3">
                    <!-- Left Column -->
                    <div class="space-y-6 lg:col-span-2">
                        <!-- Candidate Info -->
                        <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                            <h2 class="mb-4 font-semibold text-gray-900 dark:text-white">Candidate Details</h2>
                            <dl class="space-y-2 text-sm">
                                <div class="flex gap-4">
                                    <dt class="w-24 flex-none text-gray-500">Email</dt>
                                    <dd class="text-gray-900 dark:text-white">{{ applicationStore.application.candidate?.email }}</dd>
                                </div>
                                <div v-if="applicationStore.application.candidate?.phone" class="flex gap-4">
                                    <dt class="w-24 flex-none text-gray-500">Phone</dt>
                                    <dd class="text-gray-900 dark:text-white">{{ applicationStore.application.candidate.phone }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Cover Letter -->
                        <div
                            v-if="applicationStore.application.cover_letter"
                            class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800"
                        >
                            <h2 class="mb-3 font-semibold text-gray-900 dark:text-white">Cover Letter</h2>
                            <p class="text-sm leading-relaxed whitespace-pre-wrap text-gray-700 dark:text-gray-300">
                                {{ applicationStore.application.cover_letter }}
                            </p>
                        </div>

                        <!-- Resume -->
                        <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                            <h2 class="mb-3 font-semibold text-gray-900 dark:text-white">Resume</h2>
                            <a
                                v-if="applicationStore.application.resume_url"
                                :href="applicationStore.application.resume_url"
                                target="_blank"
                                class="inline-flex items-center gap-2 rounded-lg bg-blue-50 px-4 py-2 text-sm font-medium text-blue-700 hover:bg-blue-100"
                            >
                                📄 Download Resume
                            </a>
                        </div>

                        <!-- Notes -->
                        <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                            <h2 class="mb-4 font-semibold text-gray-900 dark:text-white">Internal Notes</h2>
                            <div class="mb-4 space-y-3">
                                <div
                                    v-for="note in applicationStore.application.notes"
                                    :key="note.uuid"
                                    class="rounded-lg bg-gray-50 p-4 dark:bg-gray-700/50"
                                >
                                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ note.note }}</p>
                                    <p class="mt-2 text-xs text-gray-400">{{ note.user?.name }} · {{ new Date(note.created_at).toLocaleString() }}</p>
                                </div>
                                <p v-if="!applicationStore.application.notes?.length" class="text-sm text-gray-400">No notes yet.</p>
                            </div>
                            <form class="flex gap-2" @submit.prevent="submitNote">
                                <input
                                    v-model="newNote"
                                    type="text"
                                    placeholder="Add a note..."
                                    class="flex-1 rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                />
                                <button
                                    type="submit"
                                    :disabled="addingNote || !newNote.trim()"
                                    class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-50"
                                >
                                    {{ addingNote ? '...' : 'Add' }}
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Right Column: Status Update -->
                    <div>
                        <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                            <h2 class="mb-4 font-semibold text-gray-900 dark:text-white">Update Status</h2>
                            <div class="space-y-2">
                                <button
                                    v-for="status in statusOptions"
                                    :key="status"
                                    class="w-full rounded-lg px-4 py-2 text-sm font-medium transition"
                                    :class="
                                        applicationStore.application.status === status
                                            ? (statusColors[status] ?? 'bg-gray-100')
                                            : 'border border-gray-200 text-gray-600 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300'
                                    "
                                    :disabled="updatingStatus"
                                    @click="changeStatus(status)"
                                >
                                    {{ status.charAt(0).toUpperCase() + status.slice(1) }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </AppLayout>
</template>
