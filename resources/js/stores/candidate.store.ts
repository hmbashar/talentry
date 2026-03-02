import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '@/lib/api';

export const useCandidateStore = defineStore('candidates', () => {
    const candidates = ref<any[]>([]);
    const candidate = ref<any>(null);
    const meta = ref<any>(null);
    const loading = ref(false);
    const error = ref<string | null>(null);

    async function fetchCandidates(filters: Record<string, string> = {}) {
        loading.value = true;
        error.value = null;
        try {
            const { data } = await api.get('/candidates', { params: filters });
            candidates.value = data.data;
            meta.value = data.meta;
        } catch (e: any) {
            error.value = e.response?.data?.message ?? 'Failed to load candidates.';
        } finally {
            loading.value = false;
        }
    }

    async function fetchCandidate(uuid: string) {
        loading.value = true;
        error.value = null;
        try {
            const { data } = await api.get(`/candidates/${uuid}`);
            candidate.value = data.data;
        } catch (e: any) {
            error.value = e.response?.data?.message ?? 'Failed to load candidate.';
        } finally {
            loading.value = false;
        }
    }

    return { candidates, candidate, meta, loading, error, fetchCandidates, fetchCandidate };
});
