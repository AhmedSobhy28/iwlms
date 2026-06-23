<script setup lang="ts">
import { Separator } from '@/components/ui/separator';
import { Button } from '@/components/ui/button';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { GraduationCap, Plus, Tags } from 'lucide-vue-next';

defineProps<{
    stats: {
        categories: number;
        courses: number;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Admin Dashboard',
        href: route('admin.dashboard'),
    },
];
</script>

<template>
    <Head title="Admin Dashboard" />

    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="space-y-6">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div class="space-y-0.5">
                        <h2 class="text-xl font-semibold tracking-tight">Admin dashboard</h2>
                        <p class="text-sm text-muted-foreground">Manage categories, courses, and lecture PDFs.</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <Button variant="outline" as-child>
                            <Link :href="route('admin.categories.create')">
                                <Plus class="mr-2 h-4 w-4" />
                                Add category
                            </Link>
                        </Button>
                        <Button as-child>
                            <Link :href="route('admin.courses.create')">
                                <Plus class="mr-2 h-4 w-4" />
                                Add course
                            </Link>
                        </Button>
                    </div>
                </div>
                <Separator />
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
                    <p class="text-sm text-muted-foreground">Categories</p>
                    <p class="mt-2 text-3xl font-bold">{{ stats.categories }}</p>
                </div>
                <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
                    <p class="text-sm text-muted-foreground">Courses</p>
                    <p class="mt-2 text-3xl font-bold">{{ stats.courses }}</p>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <Link
                    :href="route('admin.categories.index')"
                    class="flex items-center gap-3 rounded-xl border border-sidebar-border/70 bg-card p-6 transition hover:border-violet-500/40 hover:shadow-md dark:border-sidebar-border"
                >
                    <Tags class="h-8 w-8 text-violet-500" />
                    <div>
                        <p class="font-semibold">Manage categories</p>
                        <p class="text-sm text-muted-foreground">Create, edit, and delete course categories.</p>
                    </div>
                </Link>
                <Link
                    :href="route('admin.courses.index')"
                    class="flex items-center gap-3 rounded-xl border border-sidebar-border/70 bg-card p-6 transition hover:border-violet-500/40 hover:shadow-md dark:border-sidebar-border"
                >
                    <GraduationCap class="h-8 w-8 text-violet-500" />
                    <div>
                        <p class="font-semibold">Manage courses</p>
                        <p class="text-sm text-muted-foreground">Create courses, add lessons, and manage content.</p>
                    </div>
                </Link>
            </div>
        </div>
    </AdminLayout>
</template>
