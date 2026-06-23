<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { GraduationCap } from 'lucide-vue-next';

interface Category {
    id: number;
    name: string;
}

interface EnrolledCourse {
    id: number;
    title: string;
    slug: string;
    thumbnail: string | null;
    category: Category;
    lessons_count: number;
    completed_count: number;
    progress: number;
}

defineProps<{
    enrolledCourses: EnrolledCourse[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: route('student.dashboard') },
];

const thumbnailUrl = (path: string | null) => (path ? `/storage/${path}` : null);
</script>

<template>
    <Head title="Student Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <Heading title="My courses" description="Track your enrolled courses and lesson progress." />
                <Button as-child>
                    <Link :href="route('student.courses.index')">Browse all courses</Link>
                </Button>
            </div>

            <div v-if="enrolledCourses.length === 0" class="rounded-xl border border-dashed border-sidebar-border/70 p-8 text-center text-muted-foreground dark:border-sidebar-border">
                You haven't enrolled in any courses yet.
                <Link :href="route('student.courses.index')" class="ml-1 text-primary underline">Browse courses</Link>
                to get started.
            </div>

            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <Link
                    v-for="course in enrolledCourses"
                    :key="course.id"
                    :href="route('student.courses.show', course.slug)"
                    class="overflow-hidden rounded-xl border border-sidebar-border/70 bg-card transition hover:border-violet-500/40 hover:shadow-md dark:border-sidebar-border"
                >
                    <div class="aspect-video bg-muted/50">
                        <img
                            v-if="course.thumbnail"
                            :src="thumbnailUrl(course.thumbnail)!"
                            :alt="course.title"
                            class="h-full w-full object-cover"
                        />
                        <div v-else class="flex h-full items-center justify-center">
                            <GraduationCap class="h-12 w-12 text-muted-foreground/50" />
                        </div>
                    </div>
                    <div class="p-4">
                        <p class="text-xs text-muted-foreground">{{ course.category.name }}</p>
                        <h3 class="mt-1 font-semibold">{{ course.title }}</h3>
                        <p class="mt-2 text-sm text-muted-foreground">
                            {{ course.completed_count }} / {{ course.lessons_count }} lessons completed
                        </p>
                        <div class="mt-3 h-2 rounded-full bg-muted">
                            <div class="h-2 rounded-full bg-violet-500 transition-all" :style="{ width: `${course.progress}%` }" />
                        </div>
                        <p class="mt-1 text-xs text-muted-foreground">{{ course.progress }}% complete</p>
                    </div>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
