<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    data: number[];
    labels: string[];
    color?: string;
    height?: number;
}>();

const max = computed(() => Math.max(...props.data));
const points = computed(() => {
    return props.data.map((value, index) => {
        const x = (index / (props.data.length - 1)) * 100;
        const y = 100 - (value / max.value) * 100;
        return `${x},${y}`;
    }).join(' ');
});

const fillPath = computed(() => {
    return `0,100 ${points.value} 100,100`;
});
</script>

<template>
    <div class="w-full relative" :style="{ height: `${height || 200}px` }">
        <svg viewBox="0 0 100 100" preserveAspectRatio="none" class="w-full h-full overflow-visible">
            <defs>
                <linearGradient id="gradient" x1="0%" y1="0%" x2="0%" y2="100%">
                    <stop offset="0%" :stop-color="color || '#6366f1'" stop-opacity="0.2" />
                    <stop offset="100%" :stop-color="color || '#6366f1'" stop-opacity="0" />
                </linearGradient>
            </defs>
            <path
                :d="`M ${fillPath} Z`"
                fill="url(#gradient)"
                stroke="none"
            />
            <polyline
                :points="points"
                fill="none"
                :stroke="color || '#6366f1'"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
            />
        </svg>
        <div class="absolute bottom-0 left-0 w-full flex justify-between text-xs text-muted-foreground mt-2 translate-y-full">
            <span v-for="label in labels" :key="label">{{ label }}</span>
        </div>
    </div>
</template>
