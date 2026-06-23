<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { CheckCircle2, ChevronLeft, ChevronRight, LoaderCircle } from 'lucide-vue-next';

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
    is_video: boolean;
    is_completed: boolean;
}

interface LessonNav {
    id: number;
    title: string;
}

const props = defineProps<{
    course: Course;
    lesson: Lesson;
    previousLesson: LessonNav | null;
    nextLesson: LessonNav | null;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: route('student.dashboard') },
    { title: 'Courses', href: route('student.courses.index') },
    { title: props.course.title, href: route('student.courses.show', props.course.slug) },
    { title: props.lesson.title, href: route('student.lessons.show', [props.course.slug, props.lesson.id]) },
];

const page = usePage<{ flash: { success?: string } }>();

const completeForm = useForm({});

const markComplete = () => {
    completeForm.post(route('student.lessons.complete', props.lesson.id));
};

const embedVideoUrl = (url: string): string | null => {
    const youtubeMatch = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/);
    if (youtubeMatch) {
        return `https://www.youtube.com/embed/${youtubeMatch[1]}`;
    }

    const vimeoMatch = url.match(/vimeo\.com\/(\d+)/);
    if (vimeoMatch) {
        return `https://player.vimeo.com/video/${vimeoMatch[1]}`;
    }

    if (url.match(/\.(mp4|webm|ogg)$/i)) {
        return url;
    }

    return null;
};

const videoEmbed = props.lesson.is_video ? embedVideoUrl(props.lesson.content) : null;
</script>

<template>
    <Head :title="lesson.title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto flex w-full max-w-3xl flex-col gap-6 rounded-xl p-4">
            <div v-if="page.props.flash?.success" class="rounded-lg border border-green-500/30 bg-green-500/10 px-4 py-3 text-sm text-green-700 dark:text-green-300">
                {{ page.props.flash.success }}
            </div>

            <div class="flex items-center gap-2 text-sm text-muted-foreground">
                <Link :href="route('student.courses.show', course.slug)" class="hover:text-foreground">
                    {{ course.title }}
                </Link>
                <span>/</span>
                <span>Lesson {{ lesson.order }}</span>
            </div>

            <Heading :title="lesson.title" />

            <div v-if="lesson.is_video && videoEmbed" class="overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                <iframe
                    v-if="videoEmbed.includes('youtube') || videoEmbed.includes('vimeo')"
                    :src="videoEmbed"
                    class="aspect-video w-full"
                    allowfullscreen
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                />
                <video v-else :src="videoEmbed" controls class="aspect-video w-full" />
            </div>

            <div
                v-else
                class="prose prose-sm dark:prose-invert max-w-none rounded-xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border"
            >
                <p class="whitespace-pre-wrap">{{ lesson.content }}</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <Button v-if="!lesson.is_completed" @click="markComplete" :disabled="completeForm.processing">
                    <LoaderCircle v-if="completeForm.processing" class="mr-2 h-4 w-4 animate-spin" />
                    Mark as complete
                </Button>
                <div v-else class="flex items-center gap-2 text-sm text-green-600 dark:text-green-400">
                    <CheckCircle2 class="h-5 w-5" />
                    Lesson completed
                </div>
            </div>

            <div class="flex items-center justify-between border-t border-sidebar-border/70 pt-4 dark:border-sidebar-border">
                <Button variant="outline" size="sm" as-child :disabled="!previousLesson">
                    <Link v-if="previousLesson" :href="route('student.lessons.show', [course.slug, previousLesson.id])">
                        <ChevronLeft class="mr-1 h-4 w-4" />
                        {{ previousLesson.title }}
                    </Link>
                    <span v-else class="pointer-events-none opacity-50">
                        <ChevronLeft class="mr-1 h-4 w-4" />
                        Previous
                    </span>
                </Button>

                <Button variant="outline" size="sm" as-child :disabled="!nextLesson">
                    <Link v-if="nextLesson" :href="route('student.lessons.show', [course.slug, nextLesson.id])">
                        {{ nextLesson.title }}
                        <ChevronRight class="ml-1 h-4 w-4" />
                    </Link>
                    <span v-else class="pointer-events-none opacity-50">
                        Next
                        <ChevronRight class="ml-1 h-4 w-4" />
                    </span>
                </Button>
            </div>
        </div>
    </AppLayout>
</template>
