<script setup>
import { ref, onMounted } from "vue";
import { Head, router } from "@inertiajs/vue3";
import { Link } from "@inertiajs/vue3";
import axios from "axios";
import AppLayout from "@/Layouts/AppLayout.vue";

const loading = ref(true);

const stats = ref({
    estimaciones: 0,
    horas: 0,
    tareas: 0,
    integraciones: 0,
});

const lists = ref({
    estimaciones: [],
    tareas: [],
    fases: [],
});

const fetchDashboard = async () => {
    try {
        loading.value = true;

        const [estimacionesRes, tareasRes, integracionesRes, fasesRes] =
            await Promise.all([
                axios.get("/api/lists/estimaciones"),
                axios.get("/api/lists/tareas"),
                axios.get("/api/lists/integraciones"),
                axios.get("/api/lists/fases"),
            ]);

        stats.value.estimaciones = estimacionesRes.data.length;
        stats.value.horas = estimacionesRes.data.reduce(
            (sum, e) => sum + Number(e.total_horas ?? 0),
            0,
        );
        stats.value.tareas = tareasRes.data.length;
        stats.value.integraciones = integracionesRes.data.length;

        lists.value.estimaciones = estimacionesRes.data.slice(0, 6);
        lists.value.tareas = tareasRes.data.slice(0, 6);
        lists.value.fases = fasesRes.data;
    } catch (e) {
        console.error("Error dashboard", e);
    } finally {
        loading.value = false;
    }
};

onMounted(fetchDashboard);
</script>

<template>
    <Head title="Dashboard" />
    <AppLayout>
        <div
            class="min-h-screen bg-gradient-to-br from-gray-50 via-gray-100 to-gray-200 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 transition-colors duration-300"
        >
            <!-- Animated Background -->
            <div
                class="fixed inset-0 overflow-hidden pointer-events-none before:absolute before:inset-0 before:bg-[radial-gradient(circle_at_30%_50%,rgba(59,130,246,0.05),transparent_40%)] after:absolute after:inset-0 after:bg-[radial-gradient(circle_at_70%_30%,rgba(139,92,246,0.05),transparent_40%)]"
            ></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <!-- Header Mejorado -->
                <div
                    class="mb-10 animate-fade-in before:absolute before:-inset-1 before:rounded-2xl before:bg-gradient-to-r before:from-indigo-500 before:to-purple-600 before:opacity-0 before:blur-sm hover:before:opacity-10 transition-all duration-300 relative"
                >
                    <div
                        class="relative bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl p-8 border border-gray-200/50 dark:bg-gray-800/80 dark:border-gray-700/50"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <h1
                                    class="text-4xl md:text-5xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500 animate-gradient-shift"
                                >
                                    Dashboard
                                </h1>
                                <p
                                    class="text-gray-600 dark:text-gray-300 mt-2 flex items-center space-x-2"
                                >
                                    <span
                                        class="h-1.5 w-1.5 rounded-full bg-green-500 animate-pulse"
                                    ></span>
                                    <span
                                        >Vista general del sistema en tiempo
                                        real</span
                                    >
                                </p>
                            </div>
                            <div
                                class="hidden md:block px-4 py-2 rounded-lg bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-medium shadow-lg transform hover:scale-105 transition-transform duration-200"
                            >
                                <span class="flex items-center">
                                    <svg
                                        class="h-5 w-5 mr-2"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                    </svg>
                                    <span>{{
                                        new Date().toLocaleTimeString("es-ES", {
                                            hour: "2-digit",
                                            minute: "2-digit",
                                        })
                                    }}</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Loading Mejorado -->
                <div
                    v-if="loading"
                    class="flex flex-col items-center justify-center h-96 bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-200/50 dark:bg-gray-800/80 dark:border-gray-700/50 animate-pulse"
                >
                    <div class="relative">
                        <div
                            class="h-16 w-16 rounded-full border-4 border-indigo-200 border-t-indigo-600 animate-spin-slow"
                        ></div>
                        <div
                            class="absolute inset-0 flex items-center justify-center text-indigo-600 font-bold text-xl"
                        >
                            ⏳
                        </div>
                    </div>
                    <p
                        class="mt-6 text-gray-600 dark:text-gray-300 text-lg font-medium animate-pulse-text"
                    >
                        Cargando información del sistema...
                    </p>
                    <div
                        class="mt-4 w-64 h-2 bg-gray-200 rounded-full overflow-hidden"
                    >
                        <div
                            class="h-full bg-gradient-to-r from-indigo-500 to-purple-600 animate-shimmer"
                        ></div>
                    </div>
                </div>

                <!-- Stats Cards Mejoradas -->
                <div
                    v-else
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 animate-fade-in-up"
                >
                    <!-- Card Estimaciones -->
                    <div
                        class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-50 to-blue-50 dark:from-indigo-900/30 dark:to-blue-900/30 border border-gray-200/50 dark:border-gray-700/50 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 before:absolute before:inset-0 before:bg-gradient-to-r before:from-indigo-500 before:to-blue-500 before:opacity-0 before:transition-opacity before:duration-300 hover:before:opacity-5 after:absolute after:inset-0 after:border-2 after:border-transparent after:rounded-2xl after:pointer-events-none after:transition-all after:duration-300 group-hover:after:border-indigo-500/30"
                    >
                        <div class="relative p-7">
                            <div class="flex items-start justify-between">
                                <div>
                                    <p
                                        class="text-sm font-medium text-gray-600 dark:text-gray-300 mb-2 flex items-center space-x-2"
                                    >
                                        <svg
                                            class="h-4 w-4 text-indigo-500"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                            />
                                        </svg>
                                        <span>Estimaciones</span>
                                    </p>
                                    <p
                                        class="text-5xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-blue-600 group-hover:scale-110 transition-transform duration-300"
                                    >
                                        {{ stats.estimaciones }}
                                    </p>
                                </div>
                                <div
                                    class="p-3 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 transform group-hover:scale-110 group-hover:rotate-12 transition-transform duration-300"
                                >
                                    <svg
                                        class="h-7 w-7 text-indigo-600"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                        />
                                    </svg>
                                </div>
                            </div>
                            <div
                                class="mt-5 h-2 w-full bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden relative"
                            >
                                <div
                                    class="h-full bg-gradient-to-r from-indigo-500 to-blue-500 rounded-full transition-all duration-700 group-hover:scale-x-105 group-hover:duration-300 animate-shimmer"
                                    :style="{
                                        width: `${Math.min(stats.estimaciones * 2, 100)}%`,
                                    }"
                                ></div>
                            </div>
                        </div>
                        <div
                            class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-indigo-500 to-blue-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                        ></div>
                    </div>

                    <!-- Card Horas -->
                    <div
                        class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/30 dark:to-emerald-900/30 border border-gray-200/50 dark:border-gray-700/50 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 before:absolute before:inset-0 before:bg-gradient-to-r before:from-green-500 before:to-emerald-500 before:opacity-0 before:transition-opacity before:duration-300 hover:before:opacity-5 after:absolute after:inset-0 after:border-2 after:border-transparent after:rounded-2xl after:pointer-events-none after:transition-all after:duration-300 group-hover:after:border-green-500/30"
                    >
                        <div class="relative p-7">
                            <div class="flex items-start justify-between">
                                <div>
                                    <p
                                        class="text-sm font-medium text-gray-600 dark:text-gray-300 mb-2 flex items-center space-x-2"
                                    >
                                        <svg
                                            class="h-4 w-4 text-green-500"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                            />
                                        </svg>
                                        <span>Horas totales</span>
                                    </p>
                                    <p
                                        class="text-5xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-green-600 to-emerald-600 group-hover:scale-110 transition-transform duration-300"
                                    >
                                        {{ stats.horas }}
                                    </p>
                                </div>
                                <div
                                    class="p-3 rounded-xl bg-green-100 dark:bg-green-900/30 transform group-hover:scale-110 group-hover:rotate-12 transition-transform duration-300"
                                >
                                    <svg
                                        class="h-7 w-7 text-green-600"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                    </svg>
                                </div>
                            </div>
                            <div
                                class="mt-5 h-2 w-full bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden relative"
                            >
                                <div
                                    class="h-full bg-gradient-to-r from-green-500 to-emerald-500 rounded-full transition-all duration-700 group-hover:scale-x-105 group-hover:duration-300 animate-shimmer"
                                    :style="{
                                        width: `${Math.min(stats.horas / 10, 100)}%`,
                                    }"
                                ></div>
                            </div>
                        </div>
                        <div
                            class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-green-500 to-emerald-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                        ></div>
                    </div>

                    <!-- Card Tareas -->
                    <div
                        class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-50 to-cyan-50 dark:from-blue-900/30 dark:to-cyan-900/30 border border-gray-200/50 dark:border-gray-700/50 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 before:absolute before:inset-0 before:bg-gradient-to-r before:from-blue-500 before:to-cyan-500 before:opacity-0 before:transition-opacity before:duration-300 hover:before:opacity-5 after:absolute after:inset-0 after:border-2 after:border-transparent after:rounded-2xl after:pointer-events-none after:transition-all after:duration-300 group-hover:after:border-blue-500/30"
                    >
                        <div class="relative p-7">
                            <div class="flex items-start justify-between">
                                <div>
                                    <p
                                        class="text-sm font-medium text-gray-600 dark:text-gray-300 mb-2 flex items-center space-x-2"
                                    >
                                        <svg
                                            class="h-4 w-4 text-blue-500"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"
                                            />
                                        </svg>
                                        <span>Tareas</span>
                                    </p>
                                    <p
                                        class="text-5xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-cyan-600 group-hover:scale-110 transition-transform duration-300"
                                    >
                                        {{ stats.tareas }}
                                    </p>
                                </div>
                                <div
                                    class="p-3 rounded-xl bg-blue-100 dark:bg-blue-900/30 transform group-hover:scale-110 group-hover:rotate-12 transition-transform duration-300"
                                >
                                    <svg
                                        class="h-7 w-7 text-blue-600"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"
                                        />
                                    </svg>
                                </div>
                            </div>
                            <div
                                class="mt-5 h-2 w-full bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden relative"
                            >
                                <div
                                    class="h-full bg-gradient-to-r from-blue-500 to-cyan-500 rounded-full transition-all duration-700 group-hover:scale-x-105 group-hover:duration-300 animate-shimmer"
                                    :style="{
                                        width: `${Math.min(stats.tareas * 3, 100)}%`,
                                    }"
                                ></div>
                            </div>
                        </div>
                        <div
                            class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-500 to-cyan-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                        ></div>
                    </div>

                    <!-- Card Integraciones -->
                    <div
                        class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/30 dark:to-pink-900/30 border border-gray-200/50 dark:border-gray-700/50 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 before:absolute before:inset-0 before:bg-gradient-to-r before:from-purple-500 before:to-pink-500 before:opacity-0 before:transition-opacity before:duration-300 hover:before:opacity-5 after:absolute after:inset-0 after:border-2 after:border-transparent after:rounded-2xl after:pointer-events-none after:transition-all after:duration-300 group-hover:after:border-purple-500/30"
                    >
                        <div class="relative p-7">
                            <div class="flex items-start justify-between">
                                <div>
                                    <p
                                        class="text-sm font-medium text-gray-600 dark:text-gray-300 mb-2 flex items-center space-x-2"
                                    >
                                        <svg
                                            class="h-4 w-4 text-purple-500"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"
                                            />
                                        </svg>
                                        <span>Integraciones</span>
                                    </p>
                                    <p
                                        class="text-5xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-purple-600 to-pink-600 group-hover:scale-110 transition-transform duration-300"
                                    >
                                        {{ stats.integraciones }}
                                    </p>
                                </div>
                                <div
                                    class="p-3 rounded-xl bg-purple-100 dark:bg-purple-900/30 transform group-hover:scale-110 group-hover:rotate-12 transition-transform duration-300"
                                >
                                    <svg
                                        class="h-7 w-7 text-purple-600"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"
                                        />
                                    </svg>
                                </div>
                            </div>
                            <div
                                class="mt-5 h-2 w-full bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden relative"
                            >
                                <div
                                    class="h-full bg-gradient-to-r from-purple-500 to-pink-500 rounded-full transition-all duration-700 group-hover:scale-x-105 group-hover:duration-300 animate-shimmer"
                                    :style="{
                                        width: `${Math.min(stats.integraciones * 5, 100)}%`,
                                    }"
                                ></div>
                            </div>
                        </div>
                        <div
                            class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-purple-500 to-pink-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                        ></div>
                    </div>
                </div>

                <!-- Acceso Rápido Section -->
                <div class="mt-8 animate-fade-in-up delay-100">
                    <div
                        class="mb-6 flex items-center justify-between before:absolute before:-inset-1 before:rounded-2xl before:bg-gradient-to-r before:from-purple-500 before:to-pink-600 before:opacity-0 before:blur-sm hover:before:opacity-10 transition-all duration-300 relative"
                    >
                        <div
                            class="relative bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl px-6 py-4 border border-gray-200/50 dark:bg-gray-800/80 dark:border-gray-700/50"
                        >
                            <h2
                                class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-purple-600 via-pink-600 to-rose-500 flex items-center space-x-3"
                            >
                                <svg
                                    class="h-7 w-7 text-purple-600"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                                    />
                                </svg>
                                <span>⚡ Acceso Rápido</span>
                            </h2>
                            <p
                                class="text-sm text-gray-600 dark:text-gray-300 mt-1"
                            >
                                Navegación rápida a módulos principales
                            </p>
                        </div>
                    </div>

                    <div
                        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 animate-fade-in-up delay-200"
                    >
                        <!-- Estimación Nueva -->
                        <Link
                            :href="route('estimacion')"
                            class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-50 to-blue-50 dark:from-indigo-900/30 dark:to-blue-900/30 border-2 border-gray-200/50 dark:border-gray-700/50 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 hover:border-indigo-500 before:absolute before:inset-0 before:bg-gradient-to-r before:from-indigo-500 before:to-blue-500 before:opacity-0 before:transition-opacity before:duration-300 hover:before:opacity-5"
                        >
                            <div
                                class="relative p-6 h-full flex flex-col justify-between"
                            >
                                <div class="flex items-start justify-between">
                                    <div>
                                        <div
                                            class="flex items-center space-x-2 mb-3 text-indigo-600 dark:text-indigo-400"
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
                                                    d="M12 4v16m8-8H4"
                                                />
                                            </svg>
                                            <span class="text-sm font-medium"
                                                >NUEVO</span
                                            >
                                        </div>
                                        <h3
                                            class="text-xl font-bold text-gray-800 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors duration-200"
                                        >
                                            Crear Estimación
                                        </h3>
                                        <p
                                            class="mt-2 text-sm text-gray-600 dark:text-gray-300 group-hover:text-indigo-700 dark:group-hover:text-indigo-300 transition-colors duration-200"
                                        >
                                            Generar nueva estimación de proyecto
                                        </p>
                                    </div>
                                    <div
                                        class="p-3.5 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 transform group-hover:scale-110 group-hover:rotate-6 transition-transform duration-300"
                                    >
                                        <svg
                                            class="h-6 w-6 text-indigo-600"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 4v16m8-8H4"
                                            />
                                        </svg>
                                    </div>
                                </div>
                                <div
                                    class="mt-4 flex items-center justify-between text-sm font-medium text-indigo-600 dark:text-indigo-400"
                                >
                                    <span>Acceder ahora</span>
                                    <svg
                                        class="h-5 w-5 group-hover:translate-x-1 transition-transform duration-200"
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
                                <div
                                    class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-indigo-500 to-blue-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                                ></div>
                            </div>
                        </Link>

                        <!-- Lista de Estimaciones -->
                        <Link
                            :href="route('estimaciones.listas')"
                            class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/30 dark:to-emerald-900/30 border-2 border-gray-200/50 dark:border-gray-700/50 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 hover:border-green-500 before:absolute before:inset-0 before:bg-gradient-to-r before:from-green-500 before:to-emerald-500 before:opacity-0 before:transition-opacity before:duration-300 hover:before:opacity-5"
                        >
                            <div
                                class="relative p-6 h-full flex flex-col justify-between"
                            >
                                <div class="flex items-start justify-between">
                                    <div>
                                        <div
                                            class="flex items-center space-x-2 mb-3 text-green-600 dark:text-green-400"
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
                                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                                />
                                            </svg>
                                            <span class="text-sm font-medium"
                                                >VER</span
                                            >
                                        </div>
                                        <h3
                                            class="text-xl font-bold text-gray-800 dark:text-white group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors duration-200"
                                        >
                                            Lista de Estimaciones
                                        </h3>
                                        <p
                                            class="mt-2 text-sm text-gray-600 dark:text-gray-300 group-hover:text-green-700 dark:group-hover:text-green-300 transition-colors duration-200"
                                        >
                                            Ver y administrar todas las
                                            estimaciones
                                        </p>
                                    </div>
                                    <div
                                        class="p-3.5 rounded-xl bg-green-100 dark:bg-green-900/30 transform group-hover:scale-110 group-hover:rotate-6 transition-transform duration-300"
                                    >
                                        <svg
                                            class="h-6 w-6 text-green-600"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                            />
                                        </svg>
                                    </div>
                                </div>
                                <div
                                    class="mt-4 flex items-center justify-between text-sm font-medium text-green-600 dark:text-green-400"
                                >
                                    <span>Ver todas</span>
                                    <svg
                                        class="h-5 w-5 group-hover:translate-x-1 transition-transform duration-200"
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
                                <div
                                    class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-green-500 to-emerald-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                                ></div>
                            </div>
                        </Link>

                        <!-- Niveles -->
                        <Link
                            :href="route('niveles.index')"
                            class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/30 dark:to-pink-900/30 border-2 border-gray-200/50 dark:border-gray-700/50 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 hover:border-purple-500 before:absolute before:inset-0 before:bg-gradient-to-r before:from-purple-500 before:to-pink-500 before:opacity-0 before:transition-opacity before:duration-300 hover:before:opacity-5"
                        >
                            <div
                                class="relative p-6 h-full flex flex-col justify-between"
                            >
                                <div class="flex items-start justify-between">
                                    <div>
                                        <div
                                            class="flex items-center space-x-2 mb-3 text-purple-600 dark:text-purple-400"
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
                                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                                                />
                                            </svg>
                                            <span class="text-sm font-medium"
                                                >ADMIN</span
                                            >
                                        </div>
                                        <h3
                                            class="text-xl font-bold text-gray-800 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors duration-200"
                                        >
                                            Niveles
                                        </h3>
                                        <p
                                            class="mt-2 text-sm text-gray-600 dark:text-gray-300 group-hover:text-purple-700 dark:group-hover:text-purple-300 transition-colors duration-200"
                                        >
                                            Gestionar niveles de acceso y
                                            permisos
                                        </p>
                                    </div>
                                    <div
                                        class="p-3.5 rounded-xl bg-purple-100 dark:bg-purple-900/30 transform group-hover:scale-110 group-hover:rotate-6 transition-transform duration-300"
                                    >
                                        <svg
                                            class="h-6 w-6 text-purple-600"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                                            />
                                        </svg>
                                    </div>
                                </div>
                                <div
                                    class="mt-4 flex items-center justify-between text-sm font-medium text-purple-600 dark:text-purple-400"
                                >
                                    <span>Administrar</span>
                                    <svg
                                        class="h-5 w-5 group-hover:translate-x-1 transition-transform duration-200"
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
                                <div
                                    class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-purple-500 to-pink-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                                ></div>
                            </div>
                        </Link>

                        <!-- Fases -->
                        <Link
                            :href="route('fases.index')"
                            class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-yellow-50 to-orange-50 dark:from-yellow-900/30 dark:to-orange-900/30 border-2 border-gray-200/50 dark:border-gray-700/50 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 hover:border-yellow-500 before:absolute before:inset-0 before:bg-gradient-to-r before:from-yellow-500 before:to-orange-500 before:opacity-0 before:transition-opacity before:duration-300 hover:before:opacity-5"
                        >
                            <div
                                class="relative p-6 h-full flex flex-col justify-between"
                            >
                                <div class="flex items-start justify-between">
                                    <div>
                                        <div
                                            class="flex items-center space-x-2 mb-3 text-yellow-600 dark:text-yellow-400"
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
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                                />
                                            </svg>
                                            <span class="text-sm font-medium"
                                                >GESTIÓN</span
                                            >
                                        </div>
                                        <h3
                                            class="text-xl font-bold text-gray-800 dark:text-white group-hover:text-yellow-600 dark:group-hover:text-yellow-400 transition-colors duration-200"
                                        >
                                            Fases
                                        </h3>
                                        <p
                                            class="mt-2 text-sm text-gray-600 dark:text-gray-300 group-hover:text-yellow-700 dark:group-hover:text-yellow-300 transition-colors duration-200"
                                        >
                                            Configurar y administrar fases del
                                            proyecto
                                        </p>
                                    </div>
                                    <div
                                        class="p-3.5 rounded-xl bg-yellow-100 dark:bg-yellow-900/30 transform group-hover:scale-110 group-hover:rotate-6 transition-transform duration-300"
                                    >
                                        <svg
                                            class="h-6 w-6 text-yellow-600"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                            />
                                        </svg>
                                    </div>
                                </div>
                                <div
                                    class="mt-4 flex items-center justify-between text-sm font-medium text-yellow-600 dark:text-yellow-400"
                                >
                                    <span>Configurar</span>
                                    <svg
                                        class="h-5 w-5 group-hover:translate-x-1 transition-transform duration-200"
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
                                <div
                                    class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-yellow-500 to-orange-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                                ></div>
                            </div>
                        </Link>

                        <!-- Integraciones -->
                        <Link
                            :href="route('integraciones.index')"
                            class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-cyan-50 to-teal-50 dark:from-cyan-900/30 dark:to-teal-900/30 border-2 border-gray-200/50 dark:border-gray-700/50 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 hover:border-cyan-500 before:absolute before:inset-0 before:bg-gradient-to-r before:from-cyan-500 before:to-teal-500 before:opacity-0 before:transition-opacity before:duration-300 hover:before:opacity-5"
                        >
                            <div
                                class="relative p-6 h-full flex flex-col justify-between"
                            >
                                <div class="flex items-start justify-between">
                                    <div>
                                        <div
                                            class="flex items-center space-x-2 mb-3 text-cyan-600 dark:text-cyan-400"
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
                                                    d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"
                                                />
                                            </svg>
                                            <span class="text-sm font-medium"
                                                >API</span
                                            >
                                        </div>
                                        <h3
                                            class="text-xl font-bold text-gray-800 dark:text-white group-hover:text-cyan-600 dark:group-hover:text-cyan-400 transition-colors duration-200"
                                        >
                                            Integraciones
                                        </h3>
                                        <p
                                            class="mt-2 text-sm text-gray-600 dark:text-gray-300 group-hover:text-cyan-700 dark:group-hover:text-cyan-300 transition-colors duration-200"
                                        >
                                            Configurar conexiones y APIs
                                            externas
                                        </p>
                                    </div>
                                    <div
                                        class="p-3.5 rounded-xl bg-cyan-100 dark:bg-cyan-900/30 transform group-hover:scale-110 group-hover:rotate-6 transition-transform duration-300"
                                    >
                                        <svg
                                            class="h-6 w-6 text-cyan-600"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"
                                            />
                                        </svg>
                                    </div>
                                </div>
                                <div
                                    class="mt-4 flex items-center justify-between text-sm font-medium text-cyan-600 dark:text-cyan-400"
                                >
                                    <span>Conectar</span>
                                    <svg
                                        class="h-5 w-5 group-hover:translate-x-1 transition-transform duration-200"
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
                                <div
                                    class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-cyan-500 to-teal-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                                ></div>
                            </div>
                        </Link>

                        <!-- Tipos de Implementación -->
                        <Link
                            :href="route('tipoimplementacion.index')"
                            class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-rose-50 to-pink-50 dark:from-rose-900/30 dark:to-pink-900/30 border-2 border-gray-200/50 dark:border-gray-700/50 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 hover:border-rose-500 before:absolute before:inset-0 before:bg-gradient-to-r before:from-rose-500 before:to-pink-500 before:opacity-0 before:transition-opacity before:duration-300 hover:before:opacity-5"
                        >
                            <div
                                class="relative p-6 h-full flex flex-col justify-between"
                            >
                                <div class="flex items-start justify-between">
                                    <div>
                                        <div
                                            class="flex items-center space-x-2 mb-3 text-rose-600 dark:text-rose-400"
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
                                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"
                                                />
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                                />
                                            </svg>
                                            <span class="text-sm font-medium"
                                                >CONFIG</span
                                            >
                                        </div>
                                        <h3
                                            class="text-xl font-bold text-gray-800 dark:text-white group-hover:text-rose-600 dark:group-hover:text-rose-400 transition-colors duration-200"
                                        >
                                            Tipos de Implementación
                                        </h3>
                                        <p
                                            class="mt-2 text-sm text-gray-600 dark:text-gray-300 group-hover:text-rose-700 dark:group-hover:text-rose-300 transition-colors duration-200"
                                        >
                                            Administrar tipos y configuraciones
                                            del sistema
                                        </p>
                                    </div>
                                    <div
                                        class="p-3.5 rounded-xl bg-rose-100 dark:bg-rose-900/30 transform group-hover:scale-110 group-hover:rotate-6 transition-transform duration-300"
                                    >
                                        <svg
                                            class="h-6 w-6 text-rose-600"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"
                                            />
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                            />
                                        </svg>
                                    </div>
                                </div>
                                <div
                                    class="mt-4 flex items-center justify-between text-sm font-medium text-rose-600 dark:text-rose-400"
                                >
                                    <span>Configurar</span>
                                    <svg
                                        class="h-5 w-5 group-hover:translate-x-1 transition-transform duration-200"
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
                                <div
                                    class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-rose-500 to-pink-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                                ></div>
                            </div>
                        </Link>
                    </div>
                </div>

                <!-- Content Mejorado -->
                <div
                    class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-8 animate-fade-in-up delay-300"
                >
                    <!-- Tabla estimaciones Mejorada -->
                    <div
                        class="lg:col-span-2 bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-200/50 dark:bg-gray-800/80 dark:border-gray-700/50 overflow-hidden"
                    >
                        <div
                            class="px-6 py-5 border-b border-gray-200/50 dark:border-gray-700/50 bg-gradient-to-r from-indigo-500/5 to-transparent dark:from-indigo-900/20"
                        >
                            <h2
                                class="text-xl font-bold text-gray-800 dark:text-white flex items-center space-x-3"
                            >
                                <svg
                                    class="h-6 w-6 text-indigo-600"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                    />
                                </svg>
                                <span>Últimas estimaciones</span>
                            </h2>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead
                                    class="text-gray-500 dark:text-gray-400 bg-gray-50/50 dark:bg-gray-700/30 sticky top-0"
                                >
                                    <tr
                                        class="border-b border-gray-200/50 dark:border-gray-700/50"
                                    >
                                        <th
                                            class="text-left py-4 px-6 font-medium"
                                        >
                                            Empresa
                                        </th>
                                        <th
                                            class="text-left py-4 px-6 font-medium"
                                        >
                                            Horas
                                        </th>
                                        <th
                                            class="text-left py-4 px-6 font-medium"
                                        >
                                            Fecha
                                        </th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="divide-y divide-gray-100 dark:divide-gray-700"
                                >
                                    <tr
                                        v-for="(e, index) in lists.estimaciones"
                                        :key="e.id"
                                        class="hover:bg-indigo-50/50 dark:hover:bg-indigo-900/20 transition-colors duration-200 cursor-pointer group"
                                        @mouseenter="currentHover = e.id"
                                        @mouseleave="currentHover = null"
                                    >
                                        <td
                                            class="py-4 px-6 font-medium text-gray-800 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400"
                                        >
                                            {{ e.nombre_empresa }}
                                        </td>
                                        <td
                                            class="py-4 px-6 font-bold text-indigo-600 dark:text-indigo-400 group-hover:text-indigo-800 dark:group-hover:text-indigo-300"
                                        >
                                            {{ e.total_horas ?? 0 }}
                                        </td>
                                        <td
                                            class="py-4 px-6 text-gray-500 dark:text-gray-400 text-sm group-hover:text-indigo-600 dark:group-hover:text-indigo-400"
                                        >
                                            {{ formatDate(e.created_at) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div
                            class="px-6 py-4 bg-gray-50/50 dark:bg-gray-700/30 border-t border-gray-200/50 dark:border-gray-700/50"
                        >
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Total de registros:
                                {{ lists.estimaciones.length }}
                            </p>
                        </div>
                    </div>

                    <!-- Lateral Mejorado -->
                    <div class="space-y-6">
                        <!-- Tareas Mejorado -->
                        <div
                            class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-200/50 dark:bg-gray-800/80 dark:border-gray-700/50 overflow-hidden"
                        >
                            <div
                                class="px-6 py-5 border-b border-gray-200/50 dark:border-gray-700/50 bg-gradient-to-r from-blue-500/5 to-transparent dark:from-blue-900/20"
                            >
                                <h2
                                    class="text-xl font-bold text-gray-800 dark:text-white flex items-center space-x-3"
                                >
                                    <svg
                                        class="h-6 w-6 text-blue-600"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"
                                        />
                                    </svg>
                                    <span>Tareas</span>
                                </h2>
                            </div>

                            <div class="p-6">
                                <ul class="space-y-3">
                                    <li
                                        v-for="(t, index) in lists.tareas"
                                        :key="t.id"
                                        class="group flex items-start space-x-3 p-3 rounded-lg hover:bg-blue-50/50 dark:hover:bg-blue-900/20 transition-all duration-200 cursor-pointer border-l-3 border-transparent group-hover:border-blue-500"
                                    >
                                        <div
                                            class="flex-shrink-0 mt-0.5 h-2 w-2 rounded-full bg-blue-500 group-hover:animate-pulse"
                                        ></div>
                                        <span
                                            class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-200"
                                        >
                                            {{ t.titulo }}
                                        </span>
                                    </li>
                                </ul>
                                <div
                                    v-if="lists.tareas.length === 0"
                                    class="text-center py-8 text-gray-500 dark:text-gray-400"
                                >
                                    No hay tareas disponibles
                                </div>
                            </div>
                        </div>

                        <!-- Fases Mejorado -->
                        <div
                            class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-200/50 dark:bg-gray-800/80 dark:border-gray-700/50 overflow-hidden"
                        >
                            <div
                                class="px-6 py-5 border-b border-gray-200/50 dark:border-gray-700/50 bg-gradient-to-r from-green-500/5 to-transparent dark:from-green-900/20"
                            >
                                <h2
                                    class="text-xl font-bold text-gray-800 dark:text-white flex items-center space-x-3"
                                >
                                    <svg
                                        class="h-6 w-6 text-green-600"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                                        />
                                    </svg>
                                    <span>Fases</span>
                                </h2>
                            </div>

                            <div class="p-6">
                                <ul class="space-y-3">
                                    <li
                                        v-for="(f, index) in lists.fases"
                                        :key="f.id"
                                        class="group flex items-start space-x-3 p-3 rounded-lg hover:bg-green-50/50 dark:hover:bg-green-900/20 transition-all duration-200 cursor-pointer border-l-3 border-transparent group-hover:border-green-500"
                                    >
                                        <div
                                            class="flex-shrink-0 mt-0.5 h-2 w-2 rounded-full bg-green-500 group-hover:animate-pulse"
                                        ></div>
                                        <span
                                            class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors duration-200"
                                        >
                                            {{ f.nombre }}
                                        </span>
                                    </li>
                                </ul>
                                <div
                                    v-if="lists.fases.length === 0"
                                    class="text-center py-8 text-gray-500 dark:text-gray-400"
                                >
                                    No hay fases disponibles
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Animaciones personalizadas */
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fade-in-up {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes shimmer {
    0% {
        background-position: -1000px 0;
    }
    100% {
        background-position: 1000px 0;
    }
}

@keyframes gradient-shift {
    0%,
    100% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
}

@keyframes spin-slow {
    to {
        transform: rotate(360deg);
    }
}

@keyframes pulse-text {
    0%,
    100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.animate-fade-in {
    animation: fade-in 0.6s ease-out forwards;
}

.animate-fade-in-up {
    animation: fade-in-up 0.6s ease-out forwards;
}

.animate-fade-in-up.delay-100 {
    animation-delay: 0.1s;
}

.animate-fade-in-up.delay-200 {
    animation-delay: 0.2s;
}

.animate-fade-in-up.delay-300 {
    animation-delay: 0.3s;
}

.animate-shimmer {
    animation: shimmer 2s infinite linear;
    background: linear-gradient(
        90deg,
        transparent,
        rgba(255, 255, 255, 0.3),
        transparent
    );
    background-size: 1000px 100%;
}

.animate-gradient-shift {
    animation: gradient-shift 3s ease infinite;
    background-size: 200% 200%;
}

.animate-spin-slow {
    animation: spin-slow 2s linear infinite;
}

.animate-pulse-text {
    animation: pulse-text 1.5s ease-in-out infinite;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: transparent;
}

::-webkit-scrollbar-thumb {
    background: rgba(156, 163, 175, 0.3);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: rgba(107, 114, 128, 0.5);
}

.dark ::-webkit-scrollbar-thumb {
    background: rgba(75, 85, 99, 0.5);
}

.dark ::-webkit-scrollbar-thumb:hover {
    background: rgba(55, 65, 81, 0.7);
}
</style>

<script>
export default {
    data() {
        return {
            currentHover: null,
        };
    },
    methods: {
        formatDate(date) {
            if (!date) return "-";
            return new Date(date).toLocaleDateString("es-ES", {
                year: "numeric",
                month: "short",
                day: "numeric",
            });
        },
    },
};
</script>
