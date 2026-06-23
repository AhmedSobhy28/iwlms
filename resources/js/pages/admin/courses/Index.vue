<script setup lang="ts">
import { Separator } from '@/components/ui/separator';
import { Button } from '@/components/ui/button';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { GraduationCap, Pencil, Plus, Trash2 } from 'lucide-vue-next';

interface Category {
    id: number;
    name: string;
}

interface Course {
    id: number;
    title: string;
    slug: string;
    thumbnail: string | null;
    category_id: number;
    category?: Category;
    lessons_count?: number;
    enrollments_count?: number;
    created_at: string;
}

defineProps<{
    courses: Course[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin Dashboard', href: route('admin.dashboard') },
    { title: 'Courses', href: route('admin.courses.index') },
];

const page = usePage<{ flash: { success?: string } }>();

const thumbnailUrl = (path: string | null) => {
    if (!path) {
        return null;
    }

    return `/storage/${path}`;
};

const deleteCourse = (course: Course) => {
    if (!confirm(`Delete course "${course.title}"? This will also delete all lessons and enrollments.`)) {
        return;
    }

    router.delete(route('admin.courses.destroy', course.slug));
};
</script>

<template>
    <Head title="Courses" />

    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="space-y-6">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div class="space-y-0.5">
                        <h2 class="text-xl font-semibold tracking-tight">Courses</h2>
                        <p class="text-sm text-muted-foreground">Manage courses and their lessons.</p>
                    </div>
                    <Button as-child>
                        <Link :href="route('admin.courses.create')">
                            <Plus class="mr-2 h-4 w-4" />
                            Add course
                        </Link>
                    </Button>
                </div>
                <Separator />
            </div>

            <div v-if="page.props.flash?.success" class="rounded-lg border border-green-500/30 bg-green-500/10 px-4 py-3 text-sm text-green-700 dark:text-green-300">
                {{ page.props.flash.success }}
            </div>

            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <div v-if="courses.length === 0" class="col-span-full flex flex-col items-center gap-4 rounded-xl border border-dashed border-sidebar-border/70 p-8 text-center dark:border-sidebar-border">
                    <div>
                        <p class="font-medium">No courses yet.</p>
                        <p class="mt-1 text-sm text-muted-foreground">Create your first course so students can enroll.</p>
                    </div>
                    <Button as-child>
                        <Link :href="route('admin.courses.create')">
                            <Plus class="mr-2 h-4 w-4" />
                            Add course
                        </Link>
                    </Button>
                </div>

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
                        <p class="text-xs text-muted-foreground">{{ course.category?.name }}</p>
                        <h3 class="mt-1 font-semibold">{{ course.title }}</h3>
                        <p class="mt-2 text-sm text-muted-foreground">
                            {{ course.lessons_count ?? 0 }} lessons · {{ course.enrollments_count ?? 0 }} students
                        </p>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <Button variant="outline" size="sm" as-child>
                                <Link :href="route('admin.courses.show', course.slug)">Manage</Link>
                            </Button>
                            <Button variant="outline" size="sm" as-child>
                                <Link :href="route('admin.courses.edit', course.slug)">
                                    <Pencil class="mr-1 h-4 w-4" />
                                    Edit
                                </Link>
                            </Button>
                            <Button variant="destructive" size="sm" @click="deleteCourse(course)">
                                <Trash2 class="mr-1 h-4 w-4" />
                                Delete
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
