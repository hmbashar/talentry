import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '@/lib/api';

export const useApplicationStore = defineStore('applications', () => {
    const applications = ref<any[]>([]);
    const application = ref<any>(null);
    const meta = ref<any>(null);
    const loading = ref(false);
    const error = ref<string | null>(null);

    async function fetchApplications(filters: Record<string, string> = {}) {
        loading.value = true;
        error.value = null;
        try {
            const { data } = await api.get('/applications', { params: filters });
            applications.value = data.data;
            meta.value = data.meta;
        } catch (e: any) {
            error.value = e.response?.data?.message ?? 'Failed to load applications.';
        } finally {
            loading.value = false;
        }
    }

    async function fetchApplication(uuid: string) {
        loading.value = true;
        error.value = null;
        try {
            const { data } = await api.get(`/applications/${uuid}`);
            application.value = data.data;
        } catch (e: any) {
            error.value = e.response?.data?.message ?? 'Failed to load application.';
        } finally {
            loading.value = false;
        }
    }

    async function updateStatus(uuid: string, status: string) {
        const { data } = await api.patch(`/applications/${uuid}/status`, { status });
        application.value = data.data;
        return data.data;
    }

    async function addNote(uuid: string, note: string) {
        const { data } = await api.post(`/applications/${uuid}/notes`, { note });
        if (application.value) {
            application.value.notes = [...(application.value.notes ?? []), data.data];
        }
        return data.data;
    }

    return { applications, application, meta, loading, error, fetchApplications, fetchApplication, updateStatus, addNote };
});
