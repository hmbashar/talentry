import api from '@/lib/api';
import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useDashboardStore = defineStore('dashboard', () => {
    const stats = ref<any>(null);
    const loading = ref(false);

    async function fetchStats() {
        loading.value = true;
        try {
            const { data } = await api.get('/dashboard/stats');
            stats.value = data;
        } finally {
            loading.value = false;
        }
    }

    return { stats, loading, fetchStats };
});
