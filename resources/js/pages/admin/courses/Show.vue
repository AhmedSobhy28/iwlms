<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from 'lucide-vue-next';

interface Category {
    id: number;
    name: string;
}

interface Lesson {
    id: number;
    title: string;
    order: number;
    course_id: number;
}

interface Course {
    id: number;
    title: string;
    slug: string;
    description: string;
    thumbnail: string | null;
    category?: Category;
    lessons: Lesson[];
}

const props = defineProps<{
    course: Course;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin Dashboard', href: route('admin.dashboard') },
    { title: 'Courses', href: route('admin.courses.index') },
    { title: props.course.title, href: route('admin.courses.show', props.course.slug) },
];

const page = usePage<{ flash: { success?: string } }>();

const thumbnailUrl = (path: string | null) => (path ? `/storage/${path}` : null);

const deleteLesson = (lesson: Lesson) => {
    if (!confirm(`Delete lesson "${lesson.title}"?`)) {
        return;
    }

    router.delete(route('admin.courses.lessons.destroy', [props.course.slug, lesson.id]));
};
</script>

<template>
    <Head :title="course.title" />

    <AdminLayout :breadcrumbs="breadcrumbs">
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
                    </div>
                    <div>
                        <p class="text-sm text-muted-foreground">{{ course.category?.name }}</p>
                        <Heading :title="course.title" :description="course.description" />
                    </div>
                    <div class="flex gap-2">
                        <Button variant="outline" as-child>
                            <Link :href="route('admin.courses.edit', course.slug)">Edit course</Link>
                        </Button>
                    </div>
                </div>

                <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
                    <div class="flex items-center justify-between">
                        <h2 class="font-semibold">Lessons</h2>
                        <Button size="sm" as-child>
                            <Link :href="route('admin.courses.lessons.create', course.slug)">
                                <Plus class="mr-1 h-4 w-4" />
                                Add lesson
                            </Link>
                        </Button>
                    </div>

                    <div v-if="course.lessons.length === 0" class="mt-4 text-sm text-muted-foreground">
                        No lessons yet. Add the first lesson for this course.
                    </div>

                    <ul class="mt-4 space-y-2">
                        <li
                            v-for="lesson in course.lessons"
                            :key="lesson.id"
                            class="flex items-center justify-between rounded-lg border border-sidebar-border/50 px-3 py-2 dark:border-sidebar-border"
                        >
                            <div>
                                <span class="text-xs text-muted-foreground">#{{ lesson.order }}</span>
                                <p class="font-medium">{{ lesson.title }}</p>
                            </div>
                            <div class="flex gap-1">
                                <Button variant="ghost" size="sm" as-child>
                                    <Link :href="route('admin.courses.lessons.edit', [course.slug, lesson.id])">
                                        <Pencil class="h-4 w-4" />
                                    </Link>
                                </Button>
                                <Button variant="ghost" size="sm" @click="deleteLesson(lesson)">
                                    <Trash2 class="h-4 w-4 text-destructive" />
                                </Button>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
