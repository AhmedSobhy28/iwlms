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

interface Category {
    id: number;
    name: string;
}

const props = defineProps<{
    category: Category;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin Dashboard', href: route('admin.dashboard') },
    { title: 'Categories', href: route('admin.categories.index') },
    { title: 'Edit', href: route('admin.categories.edit', props.category.id) },
];

const form = useForm({
    name: props.category.name,
});

const submit = () => {
    form.put(route('admin.categories.update', props.category.id));
};
</script>

<template>
    <Head title="Edit Category" />

    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto flex w-full max-w-xl flex-col gap-6 rounded-xl p-4">
            <Heading title="Edit category" description="Update the category name." />

            <form @submit.prevent="submit" class="space-y-6 rounded-xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
                <div class="grid gap-2">
                    <Label for="name">Category name</Label>
                    <Input id="name" v-model="form.name" required autofocus />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="flex gap-3">
                    <Button type="submit" :disabled="form.processing">
                        <LoaderCircle v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                        Update category
                    </Button>
                    <Button variant="outline" as-child>
                        <Link :href="route('admin.categories.index')">Cancel</Link>
                    </Button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
