<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

interface Course {
    id: number;
    title: string;
    slug: string;
}

interface Lesson {
    id: number;
    title: string;
    content: string;
    order: number;
    course_id: number;
}

const props = defineProps<{
    course: Course;
    lesson: Lesson;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin Dashboard', href: route('admin.dashboard') },
    { title: 'Courses', href: route('admin.courses.index') },
    { title: props.course.title, href: route('admin.courses.show', props.course.slug) },
    { title: 'Edit lesson', href: route('admin.courses.lessons.edit', [props.course.slug, props.lesson.id]) },
];

const form = useForm({
    title: props.lesson.title,
    content: props.lesson.content,
    order: props.lesson.order,
});

const submit = () => {
    form.put(route('admin.courses.lessons.update', [props.course.slug, props.lesson.id]));
};
</script>

<template>
    <Head :title="`Edit ${lesson.title}`" />

    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto flex w-full max-w-2xl flex-col gap-6 rounded-xl p-4">
            <Heading title="Edit lesson" :description="`Update lesson in ${course.title}.`" />

            <form @submit.prevent="submit" class="space-y-6 rounded-xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
                <div class="grid gap-2">
                    <Label for="title">Lesson title</Label>
                    <Input id="title" v-model="form.title" required autofocus />
                    <InputError :message="form.errors.title" />
                </div>

                <div class="grid gap-2">
                    <Label for="order">Order</Label>
                    <Input id="order" v-model.number="form.order" type="number" min="0" required />
                    <InputError :message="form.errors.order" />
                </div>

                <div class="grid gap-2">
                    <Label for="content">Content or video URL</Label>
                    <textarea
                        id="content"
                        v-model="form.content"
                        required
                        rows="8"
                        class="flex min-h-[120px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                    />
                    <InputError :message="form.errors.content" />
                </div>

                <div class="flex gap-3">
                    <Button type="submit" :disabled="form.processing">
                        <LoaderCircle v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                        Update lesson
                    </Button>
                    <Button variant="outline" as-child>
                        <Link :href="route('admin.courses.show', course.slug)">Cancel</Link>
                    </Button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
