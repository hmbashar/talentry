<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';

const breadcrumbs = [
    {
        title: 'Candidates',
        href: '/admin/candidates',
    },
];
</script>

<template>
    <Head title="Candidates" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex-1 space-y-4 p-8 pt-6">
            <div class="flex items-center justify-between space-y-2">
                <h2 class="text-3xl font-bold tracking-tight">Candidates</h2>
            </div>
            <Card>
                <CardHeader>
                    <CardTitle>Candidates List</CardTitle>
                    <CardDescription>View and manage your candidates here.</CardDescription>
                </CardHeader>
                <CardContent>
                    <p>Candidate list coming soon...</p>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
