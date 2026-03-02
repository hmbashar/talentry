<script setup lang="ts">
import { CheckCircle, Loader2, Plus, Trash2 } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import api from '@/lib/api';

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Settings', href: '/admin/settings/homepage' },
    { title: 'Homepage', href: '/admin/settings/homepage' },
];

const loading = ref(true);
const saving = ref(false);
const saved = ref(false);

const form = ref<Record<string, any>>({
    hero_title: '',
    hero_subtitle: '',
    hero_cta_primary: '',
    hero_cta_secondary: '',
    jobs_section_title: '',
    jobs_section_subtitle: '',
    stats_section_title: '',
    stats_section_subtitle: '',
    how_it_works_title: '',
    how_it_works_subtitle: '',
    how_it_works_steps: [] as Array<{ icon: string; title: string; description: string }>,
    apply_section_title: '',
    apply_section_subtitle: '',
    cta_title: '',
    cta_subtitle: '',
    cta_button: '',
    footer_tagline: '',
    footer_links: [] as Array<{ label: string; href: string }>,
});

onMounted(async () => {
    try {
        const { data } = await api.get('/homepage-settings');
        Object.assign(form.value, data.data);
    } finally {
        loading.value = false;
    }
});

async function save() {
    saving.value = true;
    saved.value = false;
    try {
        await api.patch('/homepage-settings', form.value);
        saved.value = true;
        setTimeout(() => (saved.value = false), 3000);
    } finally {
        saving.value = false;
    }
}

function addStep() {
    form.value.how_it_works_steps = [
        ...(form.value.how_it_works_steps ?? []),
        { icon: 'CheckCircle', title: '', description: '' },
    ];
}

function removeStep(i: number) {
    form.value.how_it_works_steps = form.value.how_it_works_steps.filter((_: any, idx: number) => idx !== i);
}

function addLink() {
    form.value.footer_links = [...(form.value.footer_links ?? []), { label: '', href: '' }];
}

function removeLink(i: number) {
    form.value.footer_links = form.value.footer_links.filter((_: any, idx: number) => idx !== i);
}

const iconOptions = ['UserPlus', 'Search', 'CheckCircle', 'Briefcase', 'Star', 'Zap'];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex-1 space-y-6 p-6">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Homepage Settings</h1>
                    <p class="mt-1 text-sm text-muted-foreground">Control all editable content on the public homepage.</p>
                </div>
                <Button :disabled="saving || loading" class="gap-2" @click="save">
                    <Loader2 v-if="saving" class="h-4 w-4 animate-spin" />
                    <CheckCircle v-else-if="saved" class="h-4 w-4 text-emerald-500" />
                    {{ saved ? 'Saved!' : saving ? 'Saving…' : 'Save Changes' }}
                </Button>
            </div>

            <!-- Loading skeleton -->
            <div v-if="loading" class="space-y-4 animate-pulse">
                <div v-for="i in 4" :key="i" class="h-40 rounded-xl bg-muted"></div>
            </div>

            <template v-else>
                <!-- Hero Section -->
                <Card class="border-0 shadow-sm">
                    <CardHeader class="border-b pb-3">
                        <CardTitle class="text-base">Hero Section</CardTitle>
                        <CardDescription>Top banner shown to every visitor.</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4 pt-5">
                        <div class="space-y-1.5">
                            <Label>Headline</Label>
                            <Input v-model="form.hero_title" placeholder="Find Your Dream Job Today" />
                        </div>
                        <div class="space-y-1.5">
                            <Label>Subtext</Label>
                            <textarea v-model="form.hero_subtitle" rows="2" placeholder="Connecting top talent with world-class companies…" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-ring" /></div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="space-y-1.5">
                                <Label>Primary CTA Button</Label>
                                <Input v-model="form.hero_cta_primary" placeholder="Browse All Jobs" />
                            </div>
                            <div class="space-y-1.5">
                                <Label>Secondary CTA Button</Label>
                                <Input v-model="form.hero_cta_secondary" placeholder="Get Started Free" />
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Stats Section -->
                <Card class="border-0 shadow-sm">
                    <CardHeader class="border-b pb-3">
                        <CardTitle class="text-base">Statistics Section</CardTitle>
                        <CardDescription>Displayed below the hero. Numbers are pulled live from the database.</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4 pt-5">
                        <div class="space-y-1.5">
                            <Label>Section Title</Label>
                            <Input v-model="form.stats_section_title" placeholder="Trusted by Thousands" />
                        </div>
                        <div class="space-y-1.5">
                            <Label>Section Subtitle</Label>
                            <Input v-model="form.stats_section_subtitle" placeholder="Real numbers. Real opportunities." />
                        </div>
                    </CardContent>
                </Card>

                <!-- Jobs Section -->
                <Card class="border-0 shadow-sm">
                    <CardHeader class="border-b pb-3">
                        <CardTitle class="text-base">Job Listings Section</CardTitle>
                        <CardDescription>Shows the 6 most recent published jobs dynamically.</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4 pt-5">
                        <div class="space-y-1.5">
                            <Label>Section Title</Label>
                            <Input v-model="form.jobs_section_title" placeholder="Latest Opportunities" />
                        </div>
                        <div class="space-y-1.5">
                            <Label>Section Subtitle</Label>
                            <Input v-model="form.jobs_section_subtitle" placeholder="Hand-picked roles from leading companies." />
                        </div>
                    </CardContent>
                </Card>

                <!-- How It Works -->
                <Card class="border-0 shadow-sm">
                    <CardHeader class="border-b pb-3">
                        <CardTitle class="text-base">How It Works Section</CardTitle>
                        <CardDescription>Step-by-step guide for job seekers. Add up to 6 steps.</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4 pt-5">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="space-y-1.5">
                                <Label>Section Title</Label>
                                <Input v-model="form.how_it_works_title" placeholder="How It Works" />
                            </div>
                            <div class="space-y-1.5">
                                <Label>Section Subtitle</Label>
                                <Input v-model="form.how_it_works_subtitle" placeholder="Three simple steps to your next role." />
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div
                                v-for="(step, i) in form.how_it_works_steps"
                                :key="i"
                                class="rounded-xl border border-slate-100 bg-slate-50/50 p-4"
                            >
                                <div class="mb-3 flex items-center justify-between">
                                    <span class="text-xs font-semibold text-muted-foreground">Step {{ i + 1 }}</span>
                                    <Button variant="ghost" size="icon" class="h-7 w-7 text-destructive" @click="removeStep(i)">
                                        <Trash2 class="h-3.5 w-3.5" />
                                    </Button>
                                </div>
                                <div class="grid gap-3 sm:grid-cols-3">
                                    <div class="space-y-1.5">
                                        <Label class="text-xs">Icon</Label>
                                        <select
                                            v-model="step.icon"
                                            class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-ring"
                                        >
                                            <option v-for="opt in iconOptions" :key="opt" :value="opt">{{ opt }}</option>
                                        </select>
                                    </div>
                                    <div class="space-y-1.5">
                                        <Label class="text-xs">Title</Label>
                                        <Input v-model="step.title" placeholder="Step title" />
                                    </div>
                                    <div class="space-y-1.5 sm:col-span-1">
                                        <Label class="text-xs">Description</Label>
                                        <Input v-model="step.description" placeholder="Brief description" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <Button
                            v-if="(form.how_it_works_steps ?? []).length < 6"
                            variant="outline"
                            size="sm"
                            class="gap-1.5"
                            @click="addStep"
                        >
                            <Plus class="h-3.5 w-3.5" /> Add Step
                        </Button>
                    </CardContent>
                </Card>

                <!-- Apply Form Section -->
                <Card class="border-0 shadow-sm">
                    <CardHeader class="border-b pb-3">
                        <CardTitle class="text-base">Apply / Interest Form Section</CardTitle>
                        <CardDescription>The banner card with the interest capture form.</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4 pt-5">
                        <div class="space-y-1.5">
                            <Label>Title</Label>
                            <Input v-model="form.apply_section_title" placeholder="Jump-Start Your Career" />
                        </div>
                        <div class="space-y-1.5">
                            <Label>Subtitle</Label>
                            <textarea v-model="form.apply_section_subtitle" rows="2" placeholder="Submit a quick application…" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-ring" /></div>
                    </CardContent>
                </Card>

                <!-- CTA Band -->
                <Card class="border-0 shadow-sm">
                    <CardHeader class="border-b pb-3">
                        <CardTitle class="text-base">CTA Band</CardTitle>
                        <CardDescription>The full-width dark call-to-action strip above the footer.</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4 pt-5">
                        <div class="space-y-1.5">
                            <Label>Title</Label>
                            <Input v-model="form.cta_title" placeholder="Ready to Take the Next Step?" />
                        </div>
                        <div class="space-y-1.5">
                            <Label>Subtitle</Label>
                            <textarea v-model="form.cta_subtitle" rows="2" placeholder="Join thousands of professionals…" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-ring" /></div>
                        <div class="space-y-1.5">
                            <Label>Button Label</Label>
                            <Input v-model="form.cta_button" placeholder="Get Started Free" />
                        </div>
                    </CardContent>
                </Card>

                <!-- Footer -->
                <Card class="border-0 shadow-sm">
                    <CardHeader class="border-b pb-3">
                        <CardTitle class="text-base">Footer</CardTitle>
                        <CardDescription>Tagline text and navigation links shown in the footer.</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4 pt-5">
                        <div class="space-y-1.5">
                            <Label>Tagline</Label>
                            <Input v-model="form.footer_tagline" placeholder="Connecting talent with opportunity…" />
                        </div>

                        <div class="space-y-2">
                            <Label>Footer Links</Label>
                            <div
                                v-for="(link, i) in form.footer_links"
                                :key="i"
                                class="flex items-center gap-3"
                            >
                                <Input v-model="link.label" placeholder="Label" class="flex-1" />
                                <Input v-model="link.href" placeholder="URL (e.g. /careers)" class="flex-1" />
                                <Button variant="ghost" size="icon" class="h-9 w-9 shrink-0 text-destructive" @click="removeLink(i)">
                                    <Trash2 class="h-4 w-4" />
                                </Button>
                            </div>
                        </div>
                        <Button
                            v-if="(form.footer_links ?? []).length < 10"
                            variant="outline"
                            size="sm"
                            class="gap-1.5"
                            @click="addLink"
                        >
                            <Plus class="h-3.5 w-3.5" /> Add Link
                        </Button>
                    </CardContent>
                </Card>

                <!-- Sticky save footer -->
                <div class="flex justify-end pb-6">
                    <Button :disabled="saving" size="lg" class="gap-2 px-8" @click="save">
                        <Loader2 v-if="saving" class="h-4 w-4 animate-spin" />
                        <CheckCircle v-else-if="saved" class="h-4 w-4" />
                        {{ saved ? 'Saved!' : saving ? 'Saving…' : 'Save Changes' }}
                    </Button>
                </div>
            </template>
        </div>
    </AppLayout>
</template>
