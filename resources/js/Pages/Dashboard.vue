<template>
    <Head title="Dashboard" />
    <AppLayout>
        <!-- Main Content -->
        <div
            class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 pb-12"
        >
            <!-- Stats Grid -->
            <div class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8">
                <div
                    class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4"
                >
                    <Link
                        v-for="stat in stats"
                        :key="stat.title"
                        :href="route(stat.route)"
                        class="group block"
                    >
                        <div
                            class="glass-card h-full transform rounded-2xl p-6 transition-all duration-300 hover:scale-[1.02] hover:shadow-xl"
                        >
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <p
                                        class="text-sm font-medium text-gray-600 dark:text-gray-400"
                                    >
                                        {{ stat.title }}
                                    </p>
                                    <div
                                        class="mt-2 flex items-baseline space-x-2"
                                    >
                                        <p
                                            class="text-3xl font-bold text-gray-900 dark:text-white"
                                        >
                                            {{ stat.value }}
                                        </p>
                                        <div
                                            class="flex items-center rounded-full bg-green-100 px-2 py-1 dark:bg-green-900/30"
                                        >
                                            <svg
                                                class="h-3 w-3 text-green-600 dark:text-green-400"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M5 10l7-7m0 0l7 7m-7-7v18"
                                                />
                                            </svg>
                                            <span
                                                class="ml-1 text-xs font-medium text-green-600 dark:text-green-400"
                                            >
                                                {{ stat.change }}
                                            </span>
                                        </div>
                                    </div>
                                    <p
                                        class="mt-2 text-xs text-gray-500 dark:text-gray-400"
                                    >
                                        este mes
                                    </p>
                                </div>
                                <div
                                    :class="`rounded-xl bg-gradient-to-br ${stat.color} p-3 group-hover:scale-110 transition-transform duration-300`"
                                >
                                    <component
                                        :is="stat.icon"
                                        class="h-6 w-6 text-white"
                                    />
                                </div>
                            </div>
                            <div class="mt-4">
                                <div
                                    class="h-1 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700"
                                >
                                    <div
                                        :class="`h-full rounded-full bg-gradient-to-r ${stat.color}`"
                                        :style="{
                                            width: getProgressWidth(stat.value),
                                        }"
                                    ></div>
                                </div>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>

            <!-- Charts and Activity Section -->
            <div class="mx-auto mt-6 max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <!-- Activity Chart -->
                    <div class="lg:col-span-2">
                        <div class="glass-card h-full rounded-2xl p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3
                                        class="text-lg font-semibold text-gray-900 dark:text-white"
                                    >
                                        Actividad por Módulo
                                    </h3>
                                    <p
                                        class="text-sm text-gray-500 dark:text-gray-400"
                                    >
                                        Últimos 30 días
                                    </p>
                                </div>
                                <div class="flex space-x-2">
                                    <button
                                        v-for="period in chartPeriods"
                                        :key="period"
                                        @click="selectedPeriod = period"
                                        :class="[
                                            'px-3 py-1 text-sm rounded-lg transition-colors',
                                            selectedPeriod === period
                                                ? 'bg-blue-500 text-white'
                                                : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700',
                                        ]"
                                    >
                                        {{ period }}
                                    </button>
                                </div>
                            </div>
                            <div class="mt-6">
                                <div class="space-y-5">
                                    <div
                                        v-for="(module, idx) in moduleActivity"
                                        :key="module.name"
                                        class="group cursor-pointer"
                                        @mouseenter="hoveredModule = idx"
                                        @mouseleave="hoveredModule = -1"
                                    >
                                        <div
                                            class="flex items-center justify-between"
                                        >
                                            <div
                                                class="flex items-center space-x-3"
                                            >
                                                <div
                                                    :class="`h-10 w-10 rounded-xl flex items-center justify-center ${module.bgColor} ${hoveredModule === idx ? 'scale-110' : ''} transition-transform duration-200`"
                                                >
                                                    <component
                                                        :is="module.icon"
                                                        class="h-5 w-5 text-white"
                                                    />
                                                </div>
                                                <div>
                                                    <p
                                                        class="font-medium text-gray-900 dark:text-white"
                                                    >
                                                        {{ module.name }}
                                                    </p>
                                                    <p
                                                        class="text-sm text-gray-500 dark:text-gray-400"
                                                    >
                                                        {{ module.subtitle }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p
                                                    class="text-lg font-bold text-gray-900 dark:text-white"
                                                >
                                                    {{ module.activities }}
                                                </p>
                                                <p
                                                    class="text-sm text-gray-500 dark:text-gray-400"
                                                >
                                                    actividades
                                                </p>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <div
                                                class="h-2 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700"
                                            >
                                                <div
                                                    :class="`h-full rounded-full ${module.color} transition-all duration-500`"
                                                    :style="{
                                                        width: `${module.percentage}%`,
                                                    }"
                                                ></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="space-y-6">
                        <div class="glass-card rounded-2xl p-6">
                            <h3
                                class="text-lg font-semibold text-gray-900 dark:text-white mb-6"
                            >
                                Métricas Rápidas
                            </h3>
                            <div class="space-y-4">
                                <div
                                    v-for="metric in quickMetrics"
                                    :key="metric.label"
                                    class="flex items-center justify-between p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors"
                                >
                                    <div class="flex items-center space-x-3">
                                        <div
                                            :class="`p-2 rounded-lg ${metric.bgColor}`"
                                        >
                                            <component
                                                :is="metric.icon"
                                                class="h-4 w-4 text-white"
                                            />
                                        </div>
                                        <span
                                            class="text-sm font-medium text-gray-700 dark:text-gray-300"
                                        >
                                            {{ metric.label }}
                                        </span>
                                    </div>
                                    <span
                                        class="text-lg font-bold text-gray-900 dark:text-white"
                                    >
                                        {{ metric.value }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Performance Indicator -->
                        <div class="glass-card rounded-2xl p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3
                                    class="text-lg font-semibold text-gray-900 dark:text-white"
                                >
                                    Rendimiento
                                </h3>
                                <span
                                    class="text-2xl font-bold text-gray-900 dark:text-white"
                                >
                                    94%
                                </span>
                            </div>
                            <div class="relative pt-1">
                                <div
                                    class="flex mb-2 items-center justify-between"
                                >
                                    <div>
                                        <span
                                            class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-blue-600 bg-blue-200 dark:text-blue-200 dark:bg-blue-800"
                                        >
                                            Este mes
                                        </span>
                                    </div>
                                    <div class="text-right">
                                        <span
                                            class="text-xs font-semibold inline-block text-blue-600 dark:text-blue-400"
                                        >
                                            +12%
                                        </span>
                                    </div>
                                </div>
                                <div
                                    class="overflow-hidden h-2 mb-4 text-xs flex rounded-full bg-gray-200 dark:bg-gray-700"
                                >
                                    <div
                                        style="width: 94%"
                                        class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-gradient-to-r from-blue-500 to-purple-500"
                                    ></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions and Recent Activities -->
            <div class="mx-auto mt-6 max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <!-- Quick Actions -->
                    <div class="lg:col-span-2">
                        <div class="glass-card rounded-2xl p-6">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <h3
                                        class="text-lg font-semibold text-gray-900 dark:text-white"
                                    >
                                        Acceso Rápido
                                    </h3>
                                    <p
                                        class="text-sm text-gray-500 dark:text-gray-400"
                                    >
                                        Módulos principales del sistema
                                    </p>
                                </div>
                                <div class="flex space-x-2">
                                    <button
                                        @click="viewMode = 'grid'"
                                        :class="`p-2 rounded-lg ${viewMode === 'grid' ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'}`"
                                    >
                                        <svg
                                            class="h-5 w-5"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"
                                            />
                                        </svg>
                                    </button>
                                    <button
                                        @click="viewMode = 'list'"
                                        :class="`p-2 rounded-lg ${viewMode === 'list' ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'}`"
                                    >
                                        <svg
                                            class="h-5 w-5"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M4 6h16M4 10h16M4 14h16M4 18h16"
                                            />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div
                                :class="
                                    viewMode === 'grid'
                                        ? 'grid grid-cols-1 md:grid-cols-2 gap-4'
                                        : 'space-y-4'
                                "
                            >
                                <Link
                                    v-for="action in quickActions"
                                    :key="action.label"
                                    :href="route(action.route)"
                                    :class="[
                                        'group relative overflow-hidden rounded-xl p-4 transition-all duration-300 hover:scale-[1.02]',
                                        viewMode === 'grid'
                                            ? 'h-32'
                                            : 'min-h-[80px]',
                                        action.bgGradient,
                                    ]"
                                >
                                    <div
                                        class="relative z-10 flex h-full flex-col justify-between"
                                    >
                                        <div
                                            class="flex items-center justify-between"
                                        >
                                            <div
                                                :class="`p-2 rounded-lg ${action.iconBg} backdrop-blur-sm`"
                                            >
                                                <component
                                                    :is="action.icon"
                                                    class="h-6 w-6 text-white"
                                                />
                                            </div>
                                            <svg
                                                class="h-5 w-5 text-white/80 transform group-hover:translate-x-1 transition-transform"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M9 5l7 7-7 7"
                                                />
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-white">
                                                {{ action.label }}
                                            </h4>
                                            <p
                                                class="mt-1 text-sm text-white/80"
                                            >
                                                {{ action.description }}
                                            </p>
                                        </div>
                                    </div>
                                    <div
                                        class="absolute right-0 top-0 h-full w-32 opacity-10"
                                    >
                                        <div
                                            class="absolute inset-0 bg-gradient-to-l from-white to-transparent"
                                        ></div>
                                    </div>
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activities -->
                    <div>
                        <div class="glass-card h-full rounded-2xl p-6">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <h3
                                        class="text-lg font-semibold text-gray-900 dark:text-white"
                                    >
                                        Actividad Reciente
                                    </h3>
                                    <p
                                        class="text-sm text-gray-500 dark:text-gray-400"
                                    >
                                        Últimas acciones en el sistema
                                    </p>
                                </div>
                                <span
                                    class="rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-200"
                                >
                                    {{ recentActivities.length }} nuevas
                                </span>
                            </div>

                            <div class="space-y-4">
                                <div
                                    v-for="activity in recentActivities"
                                    :key="activity.id"
                                    class="group flex items-start space-x-3 rounded-xl p-3 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-all duration-200"
                                >
                                    <div class="relative">
                                        <div
                                            :class="`rounded-lg p-2 ${activity.bgColor} ${activity.pulse ? 'animate-pulse' : ''}`"
                                        >
                                            <component
                                                :is="activity.icon"
                                                class="h-4 w-4 text-white"
                                            />
                                        </div>
                                        <div
                                            v-if="
                                                activity.time ===
                                                'Hace 5 minutos'
                                            "
                                            class="absolute -top-1 -right-1 h-2 w-2 rounded-full bg-green-500"
                                        ></div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p
                                            class="truncate text-sm font-medium text-gray-900 dark:text-white"
                                        >
                                            {{ activity.title }}
                                        </p>
                                        <p
                                            class="mt-1 text-sm text-gray-500 dark:text-gray-400 line-clamp-2"
                                        >
                                            {{ activity.description }}
                                        </p>
                                        <p class="mt-2 text-xs text-gray-400">
                                            {{ activity.time }}
                                        </p>
                                    </div>
                                    <Link
                                        v-if="activity.route"
                                        :href="route(activity.route)"
                                        class="opacity-0 group-hover:opacity-100 transition-opacity text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                    >
                                        →
                                    </Link>
                                </div>
                            </div>

                            <!-- REMOVER o corregir esta sección si la ruta no existe -->
                            <div
                                v-if="false"
                                class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700"
                            >
                                <Link
                                    href="#"
                                    class="flex items-center justify-center text-sm font-medium text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white"
                                >
                                    Ver todas las actividades
                                    <svg
                                        class="ml-2 h-4 w-4"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M14 5l7 7m0 0l-7 7m7-7H3"
                                        />
                                    </svg>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Tasks -->
            <div
                v-if="upcomingTasks.length > 0"
                class="mx-auto mt-6 max-w-7xl px-4 sm:px-6 lg:px-8"
            >
                <div class="glass-card rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3
                                class="text-lg font-semibold text-gray-900 dark:text-white"
                            >
                                Próximas Tareas
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Pendientes por completar
                            </p>
                        </div>
                        <span
                            class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-medium text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200"
                        >
                            {{
                                upcomingTasks.filter((t) => !t.completed).length
                            }}
                            pendientes
                        </span>
                    </div>

                    <div class="space-y-3">
                        <div
                            v-for="task in upcomingTasks"
                            :key="task.id"
                            class="flex items-center justify-between rounded-lg border border-gray-200 dark:border-gray-700 p-4 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors"
                        >
                            <div class="flex items-center space-x-3">
                                <button
                                    @click="toggleTask(task.id)"
                                    :class="[
                                        'h-5 w-5 rounded-full border flex items-center justify-center transition-all',
                                        task.completed
                                            ? 'bg-green-500 border-green-500'
                                            : 'border-gray-300 dark:border-gray-600 hover:border-green-500',
                                    ]"
                                >
                                    <svg
                                        v-if="task.completed"
                                        class="h-3 w-3 text-white"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="3"
                                            d="M5 13l4 4L19 7"
                                        />
                                    </svg>
                                </button>
                                <div>
                                    <p
                                        :class="[
                                            'font-medium',
                                            task.completed
                                                ? 'text-gray-400 dark:text-gray-500 line-through'
                                                : 'text-gray-900 dark:text-white',
                                        ]"
                                    >
                                        {{ task.title }}
                                    </p>
                                    <p
                                        class="text-sm text-gray-500 dark:text-gray-400"
                                    >
                                        {{ task.dueDate }}
                                    </p>
                                </div>
                            </div>
                            <span
                                :class="[
                                    'px-2 py-1 text-xs font-medium rounded-full',
                                    task.priority === 'high'
                                        ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                                        : task.priority === 'medium'
                                          ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'
                                          : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                ]"
                            >
                                {{ task.module }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head, Link } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref, markRaw } from "vue";

// Icon Components - Usar markRaw para evitar reactividad innecesaria
const ImplementationIcon = markRaw({
    setup() {
        return () =>
            h(
                "svg",
                {
                    class: "h-5 w-5",
                    fill: "none",
                    stroke: "currentColor",
                    viewBox: "0 0 24 24",
                },
                [
                    h("path", {
                        "stroke-linecap": "round",
                        "stroke-linejoin": "round",
                        "stroke-width": "2",
                        d: "M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10",
                    }),
                ],
            );
    },
});

const IntegrationIcon = markRaw({
    setup() {
        return () =>
            h(
                "svg",
                {
                    class: "h-5 w-5",
                    fill: "none",
                    stroke: "currentColor",
                    viewBox: "0 0 24 24",
                },
                [
                    h("path", {
                        "stroke-linecap": "round",
                        "stroke-linejoin": "round",
                        "stroke-width": "2",
                        d: "M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z",
                    }),
                ],
            );
    },
});

const EstimationIcon = markRaw({
    setup() {
        return () =>
            h(
                "svg",
                {
                    class: "h-5 w-5",
                    fill: "none",
                    stroke: "currentColor",
                    viewBox: "0 0 24 24",
                },
                [
                    h("path", {
                        "stroke-linecap": "round",
                        "stroke-linejoin": "round",
                        "stroke-width": "2",
                        d: "M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z",
                    }),
                ],
            );
    },
});

const LevelIcon = markRaw({
    setup() {
        return () =>
            h(
                "svg",
                {
                    class: "h-5 w-5",
                    fill: "none",
                    stroke: "currentColor",
                    viewBox: "0 0 24 24",
                },
                [
                    h("path", {
                        "stroke-linecap": "round",
                        "stroke-linejoin": "round",
                        "stroke-width": "2",
                        d: "M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z",
                    }),
                ],
            );
    },
});

const UserIcon = markRaw({
    setup() {
        return () =>
            h(
                "svg",
                {
                    class: "h-5 w-5",
                    fill: "none",
                    stroke: "currentColor",
                    viewBox: "0 0 24 24",
                },
                [
                    h("path", {
                        "stroke-linecap": "round",
                        "stroke-linejoin": "round",
                        "stroke-width": "2",
                        d: "M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13 0h-6",
                    }),
                ],
            );
    },
});

// Reactive state
const viewMode = ref("grid");
const selectedPeriod = ref("30 días");
const hoveredModule = ref(-1);
const chartPeriods = ["7 días", "30 días", "90 días", "1 año"];

// Stats Data
const stats = ref([
    {
        title: "Tipos de Implementación",
        value: "8",
        change: "+2",
        icon: ImplementationIcon,
        color: "from-blue-500 to-cyan-500",
        route: "tipoimplementacion.index",
    },
    {
        title: "Integraciones",
        value: "15",
        change: "+5",
        icon: IntegrationIcon,
        color: "from-purple-500 to-pink-500",
        route: "integraciones.index",
    },
    {
        title: "Estimaciones",
        value: "24",
        change: "+8",
        icon: EstimationIcon,
        color: "from-yellow-500 to-orange-500",
        route: "estimaciones.listas",
    },
    {
        title: "Niveles",
        value: "5",
        change: "+1",
        icon: LevelIcon,
        color: "from-green-500 to-emerald-500",
        route: "niveles.index",
    },
]);

// Module Activity Data
const moduleActivity = ref([
    {
        name: "Tipos de Implementación",
        subtitle: "Gestión de tipos",
        activities: 45,
        percentage: 85,
        color: "bg-gradient-to-r from-blue-500 to-cyan-500",
        bgColor: "bg-gradient-to-br from-blue-500 to-cyan-500",
        icon: ImplementationIcon,
    },
    {
        name: "Integraciones",
        subtitle: "Conexiones API",
        activities: 36,
        percentage: 68,
        color: "bg-gradient-to-r from-purple-500 to-pink-500",
        bgColor: "bg-gradient-to-br from-purple-500 to-pink-500",
        icon: IntegrationIcon,
    },
    {
        name: "Estimaciones",
        subtitle: "Cálculos y proyecciones",
        activities: 28,
        percentage: 52,
        color: "bg-gradient-to-r from-yellow-500 to-orange-500",
        bgColor: "bg-gradient-to-br from-yellow-500 to-orange-500",
        icon: EstimationIcon,
    },
    {
        name: "Niveles",
        subtitle: "Configuración de acceso",
        activities: 20,
        percentage: 38,
        color: "bg-gradient-to-r from-green-500 to-emerald-500",
        bgColor: "bg-gradient-to-br from-green-500 to-emerald-500",
        icon: LevelIcon,
    },
]);

// Quick Metrics
const quickMetrics = ref([
    {
        label: "Usuarios Activos",
        value: "156",
        icon: UserIcon,
        bgColor: "bg-blue-500",
    },
    {
        label: "Tareas Completadas",
        value: "89%",
        icon: markRaw({
            setup() {
                return () =>
                    h(
                        "svg",
                        {
                            class: "h-4 w-4",
                            fill: "none",
                            stroke: "currentColor",
                            viewBox: "0 0 24 24",
                        },
                        [
                            h("path", {
                                "stroke-linecap": "round",
                                "stroke-linejoin": "round",
                                "stroke-width": "2",
                                d: "M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z",
                            }),
                        ],
                    );
            },
        }),
        bgColor: "bg-green-500",
    },
    {
        label: "Rendimiento",
        value: "94%",
        icon: markRaw({
            setup() {
                return () =>
                    h(
                        "svg",
                        {
                            class: "h-4 w-4",
                            fill: "none",
                            stroke: "currentColor",
                            viewBox: "0 0 24 24",
                        },
                        [
                            h("path", {
                                "stroke-linecap": "round",
                                "stroke-linejoin": "round",
                                strokeWidth: "2",
                                d: "M13 7h8m0 0v8m0-8l-8 8-4-4-6 6",
                            }),
                        ],
                    );
            },
        }),
        bgColor: "bg-purple-500",
    },
]);

// Quick Actions
const quickActions = ref([
    {
        label: "Tipos de Implementación",
        description: "Gestionar tipos y configuraciones",
        route: "tipoimplementacion.index",
        bgGradient: "bg-gradient-to-br from-blue-500 to-cyan-500",
        iconBg: "bg-blue-600/30",
        icon: ImplementationIcon,
    },
    {
        label: "Integraciones",
        description: "Configurar conexiones externas",
        route: "integraciones.index",
        bgGradient: "bg-gradient-to-br from-purple-500 to-pink-500",
        iconBg: "bg-purple-600/30",
        icon: IntegrationIcon,
    },
    {
        label: "Estimaciones",
        description: "Ver y crear estimaciones",
        route: "estimaciones.listas",
        bgGradient: "bg-gradient-to-br from-yellow-500 to-orange-500",
        iconBg: "bg-yellow-600/30",
        icon: EstimationIcon,
    },
    {
        label: "Niveles",
        description: "Administrar niveles de acceso",
        route: "niveles.index",
        bgGradient: "bg-gradient-to-br from-green-500 to-emerald-500",
        iconBg: "bg-green-600/30",
        icon: LevelIcon,
    },
]);

// Recent Activities
const recentActivities = ref([
    {
        id: 1,
        title: "Nuevo tipo de implementación creado",
        description: 'Se ha creado "Cloud Enterprise"',
        time: "Hace 5 minutos",
        bgColor: "bg-blue-500",
        icon: ImplementationIcon,
        route: "tipoimplementacion.index",
        pulse: true,
    },
    {
        id: 2,
        title: "Integración API actualizada",
        description: "Salesforce actualizado a v2.0",
        time: "Hace 1 hora",
        bgColor: "bg-purple-500",
        icon: IntegrationIcon,
        route: "integraciones.index",
    },
    {
        id: 3,
        title: "Estimación completada",
        description: 'Proyecto "Portal Cliente" finalizado',
        time: "Hace 2 horas",
        bgColor: "bg-yellow-500",
        icon: EstimationIcon,
        route: "estimaciones.listas",
    },
    {
        id: 4,
        title: "Nivel de acceso modificado",
        description: 'Actualizado "Administrador Avanzado"',
        time: "Hace 5 horas",
        bgColor: "bg-green-500",
        icon: LevelIcon,
        route: "niveles.index",
    },
]);

// Upcoming Tasks
const upcomingTasks = ref([
    {
        id: 1,
        title: "Revisar integración con SAP",
        dueDate: "Mañana",
        module: "Integraciones",
        priority: "high",
        completed: false,
    },
    {
        id: 2,
        title: "Crear nuevo tipo de implementación",
        dueDate: "En 2 días",
        module: "Tipos",
        priority: "medium",
        completed: false,
    },
    {
        id: 3,
        title: "Actualizar documentación de API",
        dueDate: "Esta semana",
        module: "Documentación",
        priority: "low",
        completed: true,
    },
]);

// Helper functions
const getInitials = (name) => {
    if (!name) return "U";
    return name
        .split(" ")
        .map((n) => n[0])
        .join("")
        .toUpperCase();
};

const formatDate = (date) => {
    return date.toLocaleDateString("es-ES", {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric",
    });
};

const getProgressWidth = (value) => {
    const numValue = parseInt(value);
    if (numValue <= 5) return "20%";
    if (numValue <= 10) return "40%";
    if (numValue <= 20) return "60%";
    if (numValue <= 30) return "80%";
    return "100%";
};

const toggleViewMode = () => {
    viewMode.value = viewMode.value === "grid" ? "list" : "grid";
};

const toggleTask = (taskId) => {
    const task = upcomingTasks.value.find((t) => t.id === taskId);
    if (task) {
        task.completed = !task.completed;
    }
};

// Importar h de vue
import { h } from "vue";
</script>

<style scoped>
.glass-card {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow:
        0 4px 6px -1px rgba(0, 0, 0, 0.1),
        0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.dark .glass-card {
    background: rgba(30, 41, 59, 0.7);
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow:
        0 4px 6px -1px rgba(0, 0, 0, 0.2),
        0 2px 4px -1px rgba(0, 0, 0, 0.1);
}

/* Smooth transitions */
* {
    transition:
        background-color 0.3s ease,
        border-color 0.3s ease,
        transform 0.3s ease,
        opacity 0.3s ease;
}

/* Line clamp utility */
.line-clamp-2 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.glass-card {
    animation: fadeInUp 0.6s ease-out;
}

/* Stagger animation for cards */
.glass-card:nth-child(1) {
    animation-delay: 0.1s;
}
.glass-card:nth-child(2) {
    animation-delay: 0.2s;
}
.glass-card:nth-child(3) {
    animation-delay: 0.3s;
}
.glass-card:nth-child(4) {
    animation-delay: 0.4s;
}

/* Hover effects */
.hover-scale {
    transition: transform 0.3s ease;
}

.hover-scale:hover {
    transform: scale(1.02);
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.dark ::-webkit-scrollbar-track {
    background: #374151;
}

::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Gradient text */
.gradient-text {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .glass-card {
        border-radius: 1rem;
    }

    .line-clamp-2 {
        -webkit-line-clamp: 3;
    }
}

/* Print styles */
@media print {
    .glass-card {
        background: white !important;
        border: 1px solid #e5e7eb !important;
        box-shadow: none !important;
    }

    .dark .glass-card {
        background: white !important;
        color: black !important;
    }
}
</style>
