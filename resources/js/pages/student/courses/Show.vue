<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { CheckCircle2, Circle, GraduationCap, LoaderCircle } from 'lucide-vue-next';

interface Category {
    id: number;
    name: string;
}

interface Lesson {
    id: number;
    title: string;
    order: number;
    is_completed: boolean;
}

interface Course {
    id: number;
    title: string;
    slug: string;
    description: string;
    thumbnail: string | null;
    category: Category;
    lessons: Lesson[];
}

const props = defineProps<{
    course: Course;
    isEnrolled: boolean;
    progress: number;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: route('student.dashboard') },
    { title: 'Courses', href: route('student.courses.index') },
    { title: props.course.title, href: route('student.courses.show', props.course.slug) },
];

const page = usePage<{ flash: { success?: string } }>();

const enrollForm = useForm({});

const enroll = () => {
    enrollForm.post(route('student.courses.enroll', props.course.slug));
};

const thumbnailUrl = (path: string | null) => (path ? `/storage/${path}` : null);
</script>

<template>
    <Head :title="course.title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
            <div v-if="page.props.flash?.success" class="rounded-lg border border-green-500/30 bg-green-500/10 px-4 py-3 text-sm text-green-700 dark:text-green-300">
                {{ page.props.flash.success }}
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="lg:col-span-2 space-y-4">
                    <div class="overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <img
                            v-if="course.thumbnail"
                            :src="thumbnailUrl(course.thumbnail)!"
                            :alt="course.title"
                            class="aspect-video w-full object-cover"
                        />
                        <div v-else class="flex aspect-video items-center justify-center bg-muted/50">
                            <GraduationCap class="h-16 w-16 text-muted-foreground/50" />
                        </div>
                    </div>
                    <div>
                        <p class="text-sm text-muted-foreground">{{ course.category.name }}</p>
                        <Heading :title="course.title" :description="course.description" />
                    </div>

                    <div v-if="!isEnrolled">
                        <Button @click="enroll" :disabled="enrollForm.processing">
                            <LoaderCircle v-if="enrollForm.processing" class="mr-2 h-4 w-4 animate-spin" />
                            Enroll in this course
                        </Button>
                    </div>

                    <div v-else class="rounded-xl border border-sidebar-border/70 bg-card p-4 dark:border-sidebar-border">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-muted-foreground">Your progress</span>
                            <span class="font-medium">{{ progress }}%</span>
                        </div>
                        <div class="mt-2 h-2 rounded-full bg-muted">
                            <div class="h-2 rounded-full bg-violet-500 transition-all" :style="{ width: `${progress}%` }" />
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
                    <h2 class="font-semibold">Lessons</h2>

                    <div v-if="course.lessons.length === 0" class="mt-4 text-sm text-muted-foreground">
                        No lessons available yet.
                    </div>

                    <ul v-else class="mt-4 space-y-2">
                        <li v-for="lesson in course.lessons" :key="lesson.id">
                            <Link
                                v-if="isEnrolled"
                                :href="route('student.lessons.show', [course.slug, lesson.id])"
                                class="flex items-center gap-3 rounded-lg border border-sidebar-border/50 px-3 py-2 transition hover:border-violet-500/40 dark:border-sidebar-border"
                            >
                                <CheckCircle2 v-if="lesson.is_completed" class="h-5 w-5 shrink-0 text-green-500" />
                                <Circle v-else class="h-5 w-5 shrink-0 text-muted-foreground" />
                                <div>
                                    <span class="text-xs text-muted-foreground">Lesson {{ lesson.order }}</span>
                                    <p class="font-medium">{{ lesson.title }}</p>
                                </div>
                            </Link>
                            <div
                                v-else
                                class="flex items-center gap-3 rounded-lg border border-sidebar-border/50 px-3 py-2 opacity-60 dark:border-sidebar-border"
                            >
                                <Circle class="h-5 w-5 shrink-0 text-muted-foreground" />
                                <div>
                                    <span class="text-xs text-muted-foreground">Lesson {{ lesson.order }}</span>
                                    <p class="font-medium">{{ lesson.title }}</p>
                                </div>
                            </div>
                        </li>
                    </ul>

                    <p v-if="!isEnrolled && course.lessons.length > 0" class="mt-4 text-xs text-muted-foreground">
                        Enroll to access lesson content.
                    </p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
