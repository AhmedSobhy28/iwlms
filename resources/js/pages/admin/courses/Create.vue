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
import { ref, watch } from 'vue';

interface Category {
    id: number;
    name: string;
}

defineProps<{
    categories: Category[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin Dashboard', href: route('admin.dashboard') },
    { title: 'Courses', href: route('admin.courses.index') },
    { title: 'Create', href: route('admin.courses.create') },
];

const form = useForm({
    title: '',
    slug: '',
    description: '',
    category_id: '' as string | number,
    thumbnail: null as File | null,
});

const thumbnailPreview = ref<string | null>(null);

const slugify = (value: string) =>
    value
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-');

watch(
    () => form.title,
    (title) => {
        form.slug = slugify(title);
    },
);

const onThumbnailChange = (event: Event) => {
    const file = (event.target as HTMLInputElement).files?.[0];

    if (!file) {
        form.thumbnail = null;
        thumbnailPreview.value = null;
        return;
    }

    form.thumbnail = file;
    thumbnailPreview.value = URL.createObjectURL(file);
};

const submit = () => {
    form.post(route('admin.courses.store'), {
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="Create Course" />

    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto flex w-full max-w-2xl flex-col gap-6 rounded-xl p-4">
            <Heading title="Create course" description="Add a new course with lessons for students." />

            <form @submit.prevent="submit" class="space-y-6 rounded-xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
                <div class="grid gap-2">
                    <Label for="title">Title</Label>
                    <Input id="title" v-model="form.title" required autofocus placeholder="e.g. Introduction to Laravel" />
                    <InputError :message="form.errors.title" />
                </div>

                <div class="grid gap-2">
                    <Label for="slug">Slug</Label>
                    <Input id="slug" v-model="form.slug" required placeholder="introduction-to-laravel" />
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
                        <option value="" disabled>Select a category</option>
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
                        placeholder="Describe what students will learn..."
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
                        Save course
                    </Button>
                    <Button variant="outline" as-child>
                        <Link :href="route('admin.courses.index')">Cancel</Link>
                    </Button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
