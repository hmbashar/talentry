<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
</script>

<template>
    <div
        class="flex aspect-square size-8 items-center justify-center rounded-lg bg-gradient-to-br from-violet-600 to-indigo-600 shadow-sm"
    >
        <AppLogoIcon class="size-5 fill-current text-white" />
    </div>
    <div class="ml-2 grid flex-1 text-left">
        <span class="truncate text-sm font-bold tracking-tight text-sidebar-foreground">Talentry</span>
        <span class="truncate text-[10px] text-sidebar-foreground/50 font-medium">ATS Platform</span>
    </div>
</template>
