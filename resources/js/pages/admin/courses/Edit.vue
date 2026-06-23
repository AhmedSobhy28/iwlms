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
    category_id: number;
}

const props = defineProps<{
    course: Course;
    categories: Category[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin Dashboard', href: route('admin.dashboard') },
    { title: 'Courses', href: route('admin.courses.index') },
    { title: props.course.title, href: route('admin.courses.show', props.course.slug) },
    { title: 'Edit', href: route('admin.courses.edit', props.course.slug) },
];

const form = useForm({
    title: props.course.title,
    slug: props.course.slug,
    description: props.course.description,
    category_id: props.course.category_id,
    thumbnail: null as File | null,
});

const thumbnailPreview = ref<string | null>(
    props.course.thumbnail ? `/storage/${props.course.thumbnail}` : null,
);

const onThumbnailChange = (event: Event) => {
    const file = (event.target as HTMLInputElement).files?.[0];

    if (!file) {
        form.thumbnail = null;
        return;
    }

    form.thumbnail = file;
    thumbnailPreview.value = URL.createObjectURL(file);
};

const submit = () => {
    form.transform((data) => ({
        ...data,
        _method: 'put',
    })).post(route('admin.courses.update', props.course.slug), {
        forceFormData: true,
    });
};
</script>

<template>
    <Head :title="`Edit ${course.title}`" />

    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto flex w-full max-w-2xl flex-col gap-6 rounded-xl p-4">
            <Heading title="Edit course" description="Update course details and thumbnail." />

            <form @submit.prevent="submit" class="space-y-6 rounded-xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
                <div class="grid gap-2">
                    <Label for="title">Title</Label>
                    <Input id="title" v-model="form.title" required autofocus />
                    <InputError :message="form.errors.title" />
                </div>

                <div class="grid gap-2">
                    <Label for="slug">Slug</Label>
                    <Input id="slug" v-model="form.slug" required />
                    <InputError :message="form.errors.slug" />
                </div>

                <div class="grid gap-2">
                    <Label for="category_id">Category</Label>
                    <select
                        id="category_id"
                        v-model="form.category_id"
                        required
                        class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                    >
                        <option v-for="category in categories" :key="category.id" :value="category.id">
                            {{ category.name }}
                        </option>
                    </select>
                    <InputError :message="form.errors.category_id" />
                </div>

                <div class="grid gap-2">
                    <Label for="description">Description</Label>
                    <textarea
                        id="description"
                        v-model="form.description"
                        required
                        rows="5"
                        class="flex min-h-[80px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                    />
                    <InputError :message="form.errors.description" />
                </div>

                <div class="grid gap-2">
                    <Label for="thumbnail">Thumbnail</Label>
                    <Input id="thumbnail" type="file" accept="image/*" @change="onThumbnailChange" />
                    <img v-if="thumbnailPreview" :src="thumbnailPreview" alt="Thumbnail preview" class="mt-2 h-32 w-auto rounded-lg object-cover" />
                    <InputError :message="form.errors.thumbnail" />
                </div>

                <div class="flex gap-3">
                    <Button type="submit" :disabled="form.processing">
                        <LoaderCircle v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                        Update course
                    </Button>
                    <Button variant="outline" as-child>
                        <Link :href="route('admin.courses.show', course.slug)">Cancel</Link>
                    </Button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
