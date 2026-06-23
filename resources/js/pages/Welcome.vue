<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Head, Link, usePage } from '@inertiajs/vue3';
import {
    BookOpen,
    Coffee,
    FileText,
    GraduationCap,
    PartyPopper,
    Sparkles,
    Zap,
} from 'lucide-vue-next';
import { type SharedData } from '@/types';
import { computed, onMounted, onUnmounted, ref } from 'vue';

const appName = import.meta.env.VITE_APP_NAME || 'iwLMS';
const page = usePage<SharedData>();

const dashboardHref = computed(() => {
    const user = page.props.auth.user;

    if (user?.roles?.includes('admin')) {
        return route('admin.dashboard');
    }

    if (user) {
        return route('student.dashboard');
    }

    return route('student.dashboard');
});

const taglines = [
    'Where PDFs go to die… and maybe get read.',
    'Enrol in chaos. Graduate in confusion.',
    'Your syllabus called. We sent it to voicemail.',
    '100% organic lectures. 0% attendance required.',
    'Procrastination-friendly since today.',
];

const fakeCourses = [
    {
        id: 1,
        emoji: '🛌',
        title: 'Advanced Snoozing 101',
        tag: 'Beginner',
        blurb: 'Master the art of "just five more minutes."',
        reaction: 'Enrolment pending your alarm clock.',
    },
    {
        id: 2,
        emoji: '🍜',
        title: 'Instant Noodles & Existential Dread',
        tag: 'Core',
        blurb: 'Pair every lecture PDF with sodium and regret.',
        reaction: 'You were already enrolled. Tragic.',
    },
    {
        id: 3,
        emoji: '🎭',
        title: 'Pretending to Understand Calculus',
        tag: 'Honours',
        blurb: 'Nod confidently. Cry privately. Repeat.',
        reaction: 'Prerequisite: surviving high school maths.',
    },
    {
        id: 4,
        emoji: '📄',
        title: 'PDF Hoarding: A Lifestyle',
        tag: 'Elective',
        blurb: 'Download 47 lectures. Open zero. Feel productive.',
        reaction: 'Your storage is full. Your soul is fuller.',
    },
];

const procrastinationLevels = [
    { value: 0, label: 'Productive unicorn', emoji: '🦄', color: 'from-emerald-400 to-teal-500' },
    { value: 25, label: 'Mildly distracted', emoji: '🙂', color: 'from-lime-400 to-green-500' },
    { value: 50, label: 'Classic student mode', emoji: '😅', color: 'from-amber-400 to-orange-500' },
    { value: 75, label: 'Deadline adrenaline', emoji: '😱', color: 'from-orange-500 to-red-500' },
    { value: 100, label: 'Submission at 11:59 PM', emoji: '🔥', color: 'from-red-500 to-rose-600' },
];

const mascotMessages = [
    'Hi! I ate your homework. It was PDF-flavoured.',
    'Register now. The lectures won\'t watch themselves.',
    'Fun fact: this LMS runs on caffeine and hope.',
    'Click me again. I dare you.',
    'Your GPA called. It wants a word.',
];

const currentTagline = ref(0);
const procrastination = ref(62);
const mascotIndex = ref(0);
const mascotBounce = ref(false);
const selectedCourse = ref<(typeof fakeCourses)[0] | null>(null);
const showConfetti = ref(false);
const typedTitle = ref('');
const mouseX = ref(0);
const mouseY = ref(0);
const stats = ref({ courses: 0, pdfs: 0, survivors: 0 });

let taglineTimer: ReturnType<typeof setInterval> | undefined;
let statsTimer: ReturnType<typeof setInterval> | undefined;

const fullTitle = 'Learn stuff. Eventually.';
const procrastinationState = computed(() => {
    const sorted = [...procrastinationLevels].sort((a, b) => a.value - b.value);
    return sorted.reduce((closest, level) => {
        return Math.abs(level.value - procrastination.value) < Math.abs(closest.value - procrastination.value)
            ? level
            : closest;
    });
});

const mascotMessage = computed(() => mascotMessages[mascotIndex.value % mascotMessages.length]);

function pokeMascot() {
    mascotBounce.value = true;
    mascotIndex.value = (mascotIndex.value + 1) % mascotMessages.length;
    setTimeout(() => {
        mascotBounce.value = false;
    }, 400);
}

function openCourse(course: (typeof fakeCourses)[0]) {
    selectedCourse.value = course;
}

function closeCourse() {
    selectedCourse.value = null;
}

function enrollAnyway() {
    showConfetti.value = true;
    selectedCourse.value = null;
    setTimeout(() => {
        showConfetti.value = false;
    }, 2200);
}

function handleMouseMove(event: MouseEvent) {
    mouseX.value = (event.clientX / window.innerWidth - 0.5) * 24;
    mouseY.value = (event.clientY / window.innerHeight - 0.5) * 16;
}

function animateStats() {
    const targets = { courses: 128, pdfs: 2047, survivors: 42 };
    let step = 0;
    statsTimer = setInterval(() => {
        step += 1;
        const progress = Math.min(step / 28, 1);
        const ease = 1 - Math.pow(1 - progress, 3);
        stats.value = {
            courses: Math.round(targets.courses * ease),
            pdfs: Math.round(targets.pdfs * ease),
            survivors: Math.round(targets.survivors * ease),
        };
        if (progress >= 1) {
            clearInterval(statsTimer);
        }
    }, 40);
}

function typeTitle() {
    let index = 0;
    const typeTimer = setInterval(() => {
        typedTitle.value = fullTitle.slice(0, index);
        index += 1;
        if (index > fullTitle.length) {
            clearInterval(typeTimer);
        }
    }, 55);
}

onMounted(() => {
    typeTitle();
    animateStats();
    taglineTimer = setInterval(() => {
        currentTagline.value = (currentTagline.value + 1) % taglines.length;
    }, 3200);
    window.addEventListener('mousemove', handleMouseMove);
});

onUnmounted(() => {
    clearInterval(taglineTimer);
    clearInterval(statsTimer);
    window.removeEventListener('mousemove', handleMouseMove);
});
</script>

<template>
    <Head title="Welcome to iwLMS">
        <meta name="description" content="The funniest LMS where students enrol in courses and admins upload lecture PDFs." />
    </Head>

    <div class="relative min-h-screen overflow-hidden bg-background text-foreground">
        <!-- Animated backdrop -->
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div
                class="absolute -left-24 top-10 h-72 w-72 rounded-full bg-violet-500/20 blur-3xl transition-transform duration-700 dark:bg-violet-600/15"
                :style="{ transform: `translate(${mouseX}px, ${mouseY}px)` }"
            />
            <div
                class="absolute right-0 top-1/3 h-80 w-80 rounded-full bg-amber-400/20 blur-3xl transition-transform duration-700 dark:bg-amber-500/10"
                :style="{ transform: `translate(${-mouseX}px, ${-mouseY}px)` }"
            />
            <div
                class="absolute bottom-0 left-1/3 h-64 w-64 rounded-full bg-sky-400/15 blur-3xl transition-transform duration-700 dark:bg-sky-500/10"
                :style="{ transform: `translate(${mouseX * 0.5}px, ${mouseY * 0.5}px)` }"
            />
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(255,255,255,0.35),_transparent_55%)] dark:bg-[radial-gradient(circle_at_top,_rgba(255,255,255,0.04),_transparent_55%)]" />
        </div>

        <!-- Confetti burst -->
        <div v-if="showConfetti" class="pointer-events-none fixed inset-0 z-50 flex items-center justify-center">
            <div class="animate-bounce text-6xl">🎉</div>
            <div class="absolute left-1/4 top-1/4 animate-ping text-3xl">📚</div>
            <div class="absolute right-1/4 top-1/3 animate-ping text-3xl delay-150">✅</div>
            <div class="absolute bottom-1/3 left-1/3 animate-ping text-3xl delay-300">🎓</div>
        </div>

        <div class="relative z-10 mx-auto flex min-h-screen max-w-6xl flex-col px-4 py-6 sm:px-6 lg:px-8">
            <!-- Nav -->
            <header class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-11 w-11 cursor-pointer items-center justify-center rounded-2xl bg-gradient-to-br from-violet-600 to-fuchsia-500 text-xl shadow-lg shadow-violet-500/25 transition hover:scale-110 active:scale-95"
                        :class="{ 'animate-bounce': mascotBounce }"
                        role="button"
                        tabindex="0"
                        aria-label="Talk to the iwLMS mascot"
                        @click="pokeMascot"
                        @keydown.enter="pokeMascot"
                    >
                        🦉
                    </div>
                    <div>
                        <p class="text-lg font-bold tracking-tight">{{ appName }}</p>
                        <p class="text-xs text-muted-foreground">I Will (Maybe) Learn Something</p>
                    </div>
                </div>

                <nav class="flex items-center gap-2 sm:gap-3">
                    <Link
                        v-if="page.props.auth.user"
                        :href="dashboardHref"
                        class="rounded-lg px-4 py-2 text-sm font-medium transition hover:bg-muted"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="route('admin.login')"
                            class="rounded-lg px-4 py-2 text-sm font-medium transition hover:bg-muted"
                        >
                            Admin
                        </Link>
                        <Link
                            :href="route('student.login')"
                            class="rounded-lg px-4 py-2 text-sm font-medium transition hover:bg-muted"
                        >
                            Log in
                        </Link>
                        <Button as-child class="rounded-xl bg-gradient-to-r from-violet-600 to-fuchsia-600 shadow-lg shadow-violet-500/20 hover:from-violet-500 hover:to-fuchsia-500">
                            <Link :href="route('student.register')">Join the chaos</Link>
                        </Button>
                    </template>
                </nav>
            </header>

            <!-- Mascot speech bubble -->
            <div
                class="mx-auto mt-4 max-w-md rounded-2xl border border-violet-500/20 bg-card/80 px-4 py-3 text-center text-sm shadow-sm backdrop-blur transition-all duration-300"
                :class="mascotBounce ? 'scale-105 border-violet-500/40' : ''"
            >
                <Sparkles class="mr-1 inline h-4 w-4 text-violet-500" />
                {{ mascotMessage }}
            </div>

            <!-- Hero -->
            <section class="mt-10 flex flex-col items-center text-center lg:mt-16">
                <div
                    class="mb-4 inline-flex items-center gap-2 rounded-full border border-amber-500/30 bg-amber-500/10 px-4 py-1.5 text-sm font-medium text-amber-700 dark:text-amber-300"
                >
                    <PartyPopper class="h-4 w-4" />
                    Guest mode: curiosity unlocked
                </div>

                <h1 class="max-w-3xl text-4xl font-black tracking-tight sm:text-5xl lg:text-6xl">
                    <span class="bg-gradient-to-r from-violet-600 via-fuchsia-600 to-amber-500 bg-clip-text text-transparent">
                        {{ typedTitle }}
                    </span>
                    <span class="animate-pulse text-violet-500">|</span>
                </h1>

                <p class="mt-5 min-h-[3rem] max-w-2xl text-lg text-muted-foreground transition-all duration-500 sm:text-xl">
                    {{ taglines[currentTagline] }}
                </p>

                <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                    <Button as-child size="lg" class="rounded-xl px-8 shadow-lg">
                        <Link :href="route('student.register')">
                            <GraduationCap class="mr-2 h-5 w-5" />
                            Enrol me (please)
                        </Link>
                    </Button>
                    <Button as-child variant="outline" size="lg" class="rounded-xl px-8 backdrop-blur">
                        <Link :href="route('student.login')">
                            I already have trauma here
                        </Link>
                    </Button>
                </div>
            </section>

            <!-- Stats -->
            <section class="mt-14 grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div
                    v-for="(stat, key) in [
                        { label: 'Courses uploaded', value: stats.courses, icon: BookOpen, suffix: '+' },
                        { label: 'Lecture PDFs', value: stats.pdfs, icon: FileText, suffix: '+' },
                        { label: 'Students still alive', value: stats.survivors, icon: Coffee, suffix: '%' },
                    ]"
                    :key="key"
                    class="group rounded-2xl border bg-card/70 p-6 text-center shadow-sm backdrop-blur transition hover:-translate-y-1 hover:border-violet-500/30 hover:shadow-md"
                >
                    <component :is="stat.icon" class="mx-auto mb-3 h-8 w-8 text-violet-500 transition group-hover:scale-110" />
                    <p class="text-3xl font-black tabular-nums">{{ stat.value }}{{ stat.suffix }}</p>
                    <p class="mt-1 text-sm text-muted-foreground">{{ stat.label }}</p>
                </div>
            </section>

            <!-- Procrastination meter -->
            <section class="mt-14 rounded-3xl border bg-card/80 p-6 shadow-sm backdrop-blur sm:p-8">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h2 class="flex items-center gap-2 text-2xl font-bold">
                            <Zap class="h-6 w-6 text-amber-500" />
                            Procrastination-o-meter
                        </h2>
                        <p class="mt-2 max-w-md text-muted-foreground">
                            Slide to your truth. We won't judge. (We will. Quietly.)
                        </p>
                    </div>

                    <div class="flex min-w-[220px] flex-col items-center gap-3 rounded-2xl bg-muted/50 p-5">
                        <span class="text-4xl">{{ procrastinationState.emoji }}</span>
                        <p class="text-center text-sm font-semibold">{{ procrastinationState.label }}</p>
                    </div>
                </div>

                <input
                    v-model.number="procrastination"
                    type="range"
                    min="0"
                    max="100"
                    class="mt-6 h-3 w-full cursor-pointer appearance-none rounded-full bg-muted accent-violet-600"
                    aria-label="Procrastination level"
                />
                <div
                    class="mt-4 h-2 rounded-full bg-gradient-to-r transition-all duration-300"
                    :class="procrastinationState.color"
                    :style="{ width: `${procrastination}%` }"
                />
            </section>

            <!-- Fake course catalog -->
            <section class="mt-14">
                <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 class="text-2xl font-bold">Sample courses</h2>
                        <p class="text-muted-foreground">Click a card. Admins upload the real ones later.</p>
                    </div>
                    <p class="text-sm text-muted-foreground">* not real. yet. probably.</p>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <button
                        v-for="course in fakeCourses"
                        :key="course.id"
                        type="button"
                        class="group rounded-2xl border bg-card/80 p-5 text-left shadow-sm backdrop-blur transition hover:-translate-y-1 hover:border-violet-500/40 hover:shadow-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-violet-500"
                        @click="openCourse(course)"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <span class="text-3xl transition group-hover:scale-125">{{ course.emoji }}</span>
                            <span class="rounded-full bg-violet-500/10 px-3 py-1 text-xs font-medium text-violet-600 dark:text-violet-300">
                                {{ course.tag }}
                            </span>
                        </div>
                        <h3 class="mt-4 text-lg font-bold">{{ course.title }}</h3>
                        <p class="mt-2 text-sm text-muted-foreground">{{ course.blurb }}</p>
                        <p class="mt-4 text-xs font-medium text-violet-600 opacity-0 transition group-hover:opacity-100 dark:text-violet-300">
                            Tap for admin wisdom →
                        </p>
                    </button>
                </div>
            </section>

            <!-- How it works -->
            <section class="mt-14 rounded-3xl border border-dashed bg-muted/30 p-6 sm:p-8">
                <h2 class="text-center text-2xl font-bold">How this LMS actually works</h2>
                <div class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-3">
                    <div class="rounded-2xl bg-card p-5 text-center shadow-sm">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-violet-500/10 text-2xl">👑</div>
                        <h3 class="mt-4 font-bold">Admin uploads</h3>
                        <p class="mt-2 text-sm text-muted-foreground">Courses, lectures, PDFs. The holy trinity of academic pain.</p>
                    </div>
                    <div class="rounded-2xl bg-card p-5 text-center shadow-sm">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-fuchsia-500/10 text-2xl">🎒</div>
                        <h3 class="mt-4 font-bold">Students enrol</h3>
                        <p class="mt-2 text-sm text-muted-foreground">Pick one course. Pick all courses. Live dangerously.</p>
                    </div>
                    <div class="rounded-2xl bg-card p-5 text-center shadow-sm">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-amber-500/10 text-2xl">📖</div>
                        <h3 class="mt-4 font-bold">PDFs happen</h3>
                        <p class="mt-2 text-sm text-muted-foreground">Open them. Or don't. We're an LMS, not your mom.</p>
                    </div>
                </div>
            </section>

            <!-- Final CTA -->
            <section class="my-16 rounded-3xl bg-gradient-to-br from-violet-600 via-fuchsia-600 to-amber-500 p-8 text-center text-white shadow-xl shadow-violet-500/20 sm:p-12">
                <h2 class="text-3xl font-black sm:text-4xl">Ready to pretend you'll study?</h2>
                <p class="mx-auto mt-4 max-w-xl text-white/90">
                    Create a free account, enrol in courses, and collect lecture PDFs like they're limited-edition stickers.
                </p>
                <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                    <Button as-child size="lg" variant="secondary" class="rounded-xl px-8 text-foreground">
                        <Link :href="route('student.register')">Start for free</Link>
                    </Button>
                    <Button as-child size="lg" variant="outline" class="rounded-xl border-white/40 bg-white/10 px-8 text-white hover:bg-white/20 hover:text-white">
                        <Link :href="route('student.login')">I have an account</Link>
                    </Button>
                </div>
            </section>

            <footer class="pb-8 text-center text-sm text-muted-foreground">
                © {{ new Date().getFullYear() }} {{ appName }} — powered by deadlines and delusion.
            </footer>
        </div>

        <!-- Course modal -->
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="selectedCourse"
                class="fixed inset-0 z-40 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm"
                @click.self="closeCourse"
            >
                <div class="w-full max-w-md rounded-3xl border bg-card p-6 shadow-2xl sm:p-8">
                    <div class="flex items-start justify-between gap-4">
                        <span class="text-5xl">{{ selectedCourse.emoji }}</span>
                        <button
                            type="button"
                            class="rounded-lg px-2 py-1 text-muted-foreground transition hover:bg-muted hover:text-foreground"
                            aria-label="Close"
                            @click="closeCourse"
                        >
                            ✕
                        </button>
                    </div>
                    <h3 class="mt-4 text-2xl font-bold">{{ selectedCourse.title }}</h3>
                    <p class="mt-2 text-muted-foreground">{{ selectedCourse.blurb }}</p>
                    <p class="mt-4 rounded-xl bg-violet-500/10 p-4 text-sm font-medium text-violet-700 dark:text-violet-300">
                        {{ selectedCourse.reaction }}
                    </p>
                    <div class="mt-6 flex flex-wrap gap-3">
                        <Button class="rounded-xl" @click="enrollAnyway">Enrol anyway</Button>
                        <Button variant="outline" class="rounded-xl" as-child>
                            <Link :href="route('student.register')">Register to unlock real courses</Link>
                        </Button>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>
