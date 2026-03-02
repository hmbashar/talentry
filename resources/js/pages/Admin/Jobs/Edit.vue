<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { AlertCircle, ArrowLeft, Briefcase, Calendar, Clock, MapPin, Save } from 'lucide-vue-next';
import { onMounted, ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { useJobStore } from '@/stores/job.store';

const props = defineProps<{ uuid: string }>();

const jobStore = useJobStore();

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Jobs', href: '/admin/jobs' },
    { title: 'Edit', href: `/admin/jobs/${props.uuid}/edit` },
];

const form = ref({
    title: '',
    description: '',
    location: '',
    employment_type: '',
    deadline: '',
});

const error = ref<string | null>(null);
const submitting = ref(false);

const employmentTypes = [
    { value: 'full_time', label: 'Full-Time' },
    { value: 'part_time', label: 'Part-Time' },
    { value: 'contract', label: 'Contract' },
    { value: 'remote', label: 'Remote' },
    { value: 'internship', label: 'Internship' },
];

onMounted(() => jobStore.fetchJob(props.uuid));

watch(
    () => jobStore.job,
    (job) => {
        if (job) {
            form.value = {
                title: job.title ?? '',
                description: job.description ?? '',
                location: job.location ?? '',
                employment_type: job.employment_type ?? '',
                deadline: job.deadline ?? '',
            };
        }
    },
    { immediate: true },
);

async function submit() {
    submitting.value = true;
    error.value = null;
    try {
        await jobStore.updateJob(props.uuid, form.value);
        router.visit('/admin/jobs');
    } catch (e: any) {
        error.value = e.response?.data?.message ?? 'Failed to update job posting.';
    } finally {
        submitting.value = false;
    }
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex-1 space-y-6 p-6">
            <!-- Back -->
            <Button variant="ghost" size="sm" class="-ml-2 gap-2 text-muted-foreground hover:text-foreground" as-child>
                <a href="/admin/jobs">
                    <ArrowLeft class="h-4 w-4" />
                    Back to Jobs
                </a>
            </Button>

            <!-- Loading skeleton -->
            <div v-if="jobStore.loading" class="mx-auto max-w-3xl space-y-4 animate-pulse">
                <div class="h-7 w-56 rounded-lg bg-muted" />
                <div class="h-4 w-80 rounded bg-muted/70" />
                <div class="h-64 rounded-xl bg-muted" />
            </div>

            <template v-else-if="jobStore.job">
                <!-- Header -->
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-linear-to-br from-violet-500 to-indigo-600 text-white shadow-sm">
                        <Briefcase class="h-6 w-6" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight">Edit Job Posting</h1>
                        <p class="mt-1 text-sm text-muted-foreground">Update the details for <span class="font-medium text-foreground">{{ jobStore.job.title }}</span></p>
                    </div>
                </div>

                <form class="mx-auto max-w-3xl" @submit.prevent="submit">
                    <div class="space-y-5">
                        <!-- Error -->
                        <div v-if="error" class="flex items-center gap-2 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-400">
                            <AlertCircle class="h-4 w-4 shrink-0" />
                            {{ error }}
                        </div>

                        <!-- Job Details -->
                        <Card class="border-0 shadow-sm">
                            <CardHeader class="border-b pb-4">
                                <CardTitle class="text-sm font-semibold">Job Details</CardTitle>
                                <CardDescription>Core information about the position.</CardDescription>
                            </CardHeader>
                            <CardContent class="space-y-5 pt-5">
                                <!-- Title -->
                                <div class="space-y-1.5">
                                    <label class="flex items-center gap-1.5 text-sm font-medium">
                                        <Briefcase class="h-3.5 w-3.5 text-muted-foreground" />
                                        Job Title <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        v-model="form.title"
                                        type="text"
                                        required
                                        placeholder="e.g. Senior Frontend Engineer"
                                        class="w-full rounded-lg border border-border bg-background px-3 py-2 text-sm outline-none transition focus:border-violet-400 focus:ring-2 focus:ring-violet-100 dark:focus:ring-violet-900/40"
                                    />
                                </div>

                                <!-- Description -->
                                <div class="space-y-1.5">
                                    <label class="text-sm font-medium">
                                        Description <span class="text-red-500">*</span>
                                    </label>
                                    <textarea
                                        v-model="form.description"
                                        rows="10"
                                        required
                                        placeholder="Describe the role, responsibilities, and requirements…"
                                        class="w-full rounded-lg border border-border bg-background px-3 py-2 text-sm outline-none transition focus:border-violet-400 focus:ring-2 focus:ring-violet-100 dark:focus:ring-violet-900/40"
                                    />
                                </div>
                            </CardContent>
                        </Card>

                        <!-- Position Info -->
                        <Card class="border-0 shadow-sm">
                            <CardHeader class="border-b pb-4">
                                <CardTitle class="text-sm font-semibold">Position Info</CardTitle>
                                <CardDescription>Location, type, and deadline.</CardDescription>
                            </CardHeader>
                            <CardContent class="grid gap-5 pt-5 sm:grid-cols-2">
                                <!-- Location -->
                                <div class="space-y-1.5">
                                    <label class="flex items-center gap-1.5 text-sm font-medium">
                                        <MapPin class="h-3.5 w-3.5 text-muted-foreground" />
                                        Location <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        v-model="form.location"
                                        type="text"
                                        required
                                        placeholder="e.g. Remote, New York, NY"
                                        class="w-full rounded-lg border border-border bg-background px-3 py-2 text-sm outline-none transition focus:border-violet-400 focus:ring-2 focus:ring-violet-100 dark:focus:ring-violet-900/40"
                                    />
                                </div>

                                <!-- Employment Type -->
                                <div class="space-y-1.5">
                                    <label class="flex items-center gap-1.5 text-sm font-medium">
                                        <Clock class="h-3.5 w-3.5 text-muted-foreground" />
                                        Employment Type
                                    </label>
                                    <select
                                        v-model="form.employment_type"
                                        class="w-full rounded-lg border border-border bg-background px-3 py-2 text-sm outline-none transition focus:border-violet-400 focus:ring-2 focus:ring-violet-100 dark:focus:ring-violet-900/40"
                                    >
                                        <option value="">Select type…</option>
                                        <option v-for="t in employmentTypes" :key="t.value" :value="t.value">{{ t.label }}</option>
                                    </select>
                                </div>

                                <!-- Deadline -->
                                <div class="space-y-1.5 sm:col-span-2">
                                    <label class="flex items-center gap-1.5 text-sm font-medium">
                                        <Calendar class="h-3.5 w-3.5 text-muted-foreground" />
                                        Application Deadline
                                        <span class="text-xs font-normal text-muted-foreground">(optional)</span>
                                    </label>
                                    <input
                                        v-model="form.deadline"
                                        type="date"
                                        class="w-full rounded-lg border border-border bg-background px-3 py-2 text-sm outline-none transition focus:border-violet-400 focus:ring-2 focus:ring-violet-100 dark:focus:ring-violet-900/40 sm:max-w-xs"
                                    />
                                </div>
                            </CardContent>
                        </Card>

                        <!-- Actions -->
                        <div class="flex items-center justify-between rounded-xl border border-border bg-card px-5 py-4">
                            <Button variant="ghost" class="gap-2 text-muted-foreground" as-child>
                                <a href="/admin/jobs">
                                    <ArrowLeft class="h-4 w-4" />
                                    Cancel
                                </a>
                            </Button>
                            <Button
                                type="submit"
                                class="gap-2 bg-violet-600 text-white hover:bg-violet-700"
                                :disabled="submitting"
                            >
                                <Save class="h-4 w-4" />
                                {{ submitting ? 'Saving…' : 'Save Changes' }}
                            </Button>
                        </div>
                    </div>
                </form>
            </template>

            <!-- Job not found -->
            <div v-else class="flex flex-col items-center justify-center py-20">
                <div class="mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-muted">
                    <Briefcase class="h-7 w-7 text-muted-foreground" />
                </div>
                <p class="text-sm font-medium">Job posting not found</p>
            </div>
        </div>
    </AppLayout>
</template>
