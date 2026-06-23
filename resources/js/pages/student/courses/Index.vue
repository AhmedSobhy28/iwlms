<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { BookOpen, GraduationCap, LoaderCircle } from 'lucide-vue-next';
import { ref } from 'vue';

interface Category {
    id: number;
    name: string;
}

interface Course {
    id: number;
    title: string;
    slug: string;
    description: string;
    thumbnail: string | null;
    category: Category;
    lessons_count: number;
    completed_count: number;
    progress: number;
    is_enrolled: boolean;
}

defineProps<{
    courses: Course[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: route('student.dashboard') },
    { title: 'Courses', href: route('student.courses.index') },
];

const enrollingCourseSlug = ref<string | null>(null);

const thumbnailUrl = (path: string | null) => (path ? `/storage/${path}` : null);

const enroll = (course: Course) => {
    enrollingCourseSlug.value = course.slug;

    router.post(
        route('student.courses.enroll', course.slug),
        {},
        {
            preserveScroll: true,
            onFinish: () => {
                enrollingCourseSlug.value = null;
            },
        },
    );
};
</script>

<template>
    <Head title="Browse Courses" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Heading title="Browse courses" description="Discover courses and enroll to start learning." />

            <div v-if="courses.length === 0" class="rounded-xl border border-dashed border-sidebar-border/70 p-8 text-center text-muted-foreground dark:border-sidebar-border">
                No courses available yet. Check back soon.
            </div>

            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="course in courses"
                    :key="course.id"
                    class="overflow-hidden rounded-xl border border-sidebar-border/70 bg-card dark:border-sidebar-border"
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
                        <div class="flex items-center gap-2">
                            <p class="text-xs text-muted-foreground">{{ course.category.name }}</p>
                            <span
                                v-if="course.is_enrolled"
                                class="rounded-full bg-green-500/10 px-2 py-0.5 text-xs text-green-700 dark:text-green-300"
                            >
                                Enrolled
                            </span>
                        </div>
                        <h3 class="mt-1 font-semibold">{{ course.title }}</h3>
                        <p class="mt-2 line-clamp-2 text-sm text-muted-foreground">{{ course.description }}</p>
                        <p class="mt-2 text-xs text-muted-foreground">{{ course.lessons_count }} lessons</p>

                        <div v-if="course.is_enrolled" class="mt-3">
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-muted-foreground">Progress</span>
                                <span class="font-medium">{{ course.progress }}%</span>
                            </div>
                            <div class="mt-2 h-2 rounded-full bg-muted">
                                <div class="h-2 rounded-full bg-violet-500 transition-all" :style="{ width: `${course.progress}%` }" />
                            </div>
                            <p class="mt-1 text-xs text-muted-foreground">
                                {{ course.completed_count }} / {{ course.lessons_count }} lessons completed
                            </p>
                        </div>

                        <div class="mt-4 flex flex-wrap gap-2">
                            <Button variant="outline" size="sm" as-child>
                                <Link :href="route('student.courses.show', course.slug)">
                                    <BookOpen class="mr-2 h-4 w-4" />
                                    View course
                                </Link>
                            </Button>
                            <Button
                                v-if="!course.is_enrolled"
                                size="sm"
                                :disabled="enrollingCourseSlug === course.slug"
                                @click="enroll(course)"
                            >
                                <LoaderCircle v-if="enrollingCourseSlug === course.slug" class="mr-2 h-4 w-4 animate-spin" />
                                Enroll
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
