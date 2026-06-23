<script setup lang="ts">
import { Separator } from '@/components/ui/separator';
import { Button } from '@/components/ui/button';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from 'lucide-vue-next';

interface Category {
    id: number;
    name: string;
    created_at: string;
}

defineProps<{
    categories: Category[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin Dashboard', href: route('admin.dashboard') },
    { title: 'Categories', href: route('admin.categories.index') },
];

const page = usePage<{ flash: { success?: string } }>();

const deleteCategory = (category: Category) => {
    if (!confirm(`Delete category "${category.name}"?`)) {
        return;
    }

    router.delete(route('admin.categories.destroy', category.id));
};
</script>

<template>
    <Head title="Categories" />

    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="space-y-6">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div class="space-y-0.5">
                        <h2 class="text-xl font-semibold tracking-tight">Categories</h2>
                        <p class="text-sm text-muted-foreground">Organize courses into categories.</p>
                    </div>
                    <Button as-child>
                        <Link :href="route('admin.categories.create')">
                            <Plus class="mr-2 h-4 w-4" />
                            Add category
                        </Link>
                    </Button>
                </div>
                <Separator />
            </div>

            <div v-if="page.props.flash.success" class="rounded-lg border border-green-500/30 bg-green-500/10 px-4 py-3 text-sm text-green-700 dark:text-green-300">
                {{ page.props.flash.success }}
            </div>

            <div class="overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                <table class="w-full text-left text-sm">
                    <thead class="border-b bg-muted/50">
                        <tr>
                            <th class="px-4 py-3 font-medium">Name</th>
                            <th class="px-4 py-3 font-medium">Created</th>
                            <th class="px-4 py-3 text-right font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="categories.length === 0">
                            <td colspan="3" class="px-4 py-8">
                                <div class="flex flex-col items-center gap-4 text-center">
                                    <div>
                                        <p class="font-medium">No categories yet.</p>
                                        <p class="mt-1 text-sm text-muted-foreground">Create a category before grouping courses.</p>
                                    </div>
                                    <Button as-child>
                                        <Link :href="route('admin.categories.create')">
                                            <Plus class="mr-2 h-4 w-4" />
                                            Add category
                                        </Link>
                                    </Button>
                                </div>
                            </td>
                        </tr>
                        <tr v-for="category in categories" :key="category.id" class="border-b last:border-b-0">
                            <td class="px-4 py-3 font-medium">{{ category.name }}</td>
                            <td class="px-4 py-3 text-muted-foreground">{{ new Date(category.created_at).toLocaleDateString() }}</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <Button variant="outline" size="sm" as-child>
                                        <Link :href="route('admin.categories.edit', category.id)">
                                            <Pencil class="mr-1 h-4 w-4" />
                                            Edit
                                        </Link>
                                    </Button>
                                    <Button variant="destructive" size="sm" @click="deleteCategory(category)">
                                        <Trash2 class="mr-1 h-4 w-4" />
                                        Delete
                                    </Button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>
