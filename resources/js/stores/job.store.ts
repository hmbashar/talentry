import api from '@/lib/api';
import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useJobStore = defineStore('jobs', () => {
    const jobs = ref<any[]>([]);
    const job = ref<any>(null);
    const meta = ref<any>(null);
    const loading = ref(false);
    const error = ref<string | null>(null);

    async function fetchJobs(filters: Record<string, string> = {}) {
        loading.value = true;
        error.value = null;
        try {
            const { data } = await api.get('/jobs', { params: filters });
            jobs.value = data.data;
            meta.value = data.meta;
        } catch (e: any) {
            error.value = e.response?.data?.message ?? 'Failed to load jobs.';
        } finally {
            loading.value = false;
        }
    }

    async function fetchJob(uuid: string) {
        loading.value = true;
        error.value = null;
        try {
            const { data } = await api.get(`/jobs/${uuid}`);
            job.value = data.data;
        } catch (e: any) {
            error.value = e.response?.data?.message ?? 'Failed to load job.';
        } finally {
            loading.value = false;
        }
    }

    async function createJob(payload: Record<string, any>) {
        const { data } = await api.post('/jobs', payload);
        return data.data;
    }

    async function updateJob(uuid: string, payload: Record<string, any>) {
        const { data } = await api.put(`/jobs/${uuid}`, payload);
        return data.data;
    }

    async function deleteJob(uuid: string) {
        await api.delete(`/jobs/${uuid}`);
        jobs.value = jobs.value.filter((j) => j.uuid !== uuid);
    }

    async function publishJob(uuid: string) {
        const { data } = await api.patch(`/jobs/${uuid}/publish`);
        const idx = jobs.value.findIndex((j) => j.uuid === uuid);
        if (idx !== -1) {
            jobs.value[idx] = data.data;
        }
        return data.data;
    }

    async function draftJob(uuid: string) {
        const { data } = await api.patch(`/jobs/${uuid}/draft`);
        const idx = jobs.value.findIndex((j) => j.uuid === uuid);
        if (idx !== -1) {
            jobs.value[idx] = data.data;
        }
        return data.data;
    }

    return { jobs, job, meta, loading, error, fetchJobs, fetchJob, createJob, updateJob, deleteJob, publishJob, draftJob };
});
