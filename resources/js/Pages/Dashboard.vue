<script setup>
import { ref, onMounted, computed, nextTick } from "vue";
import { Link } from "@inertiajs/vue3";
import axios from "axios";
import { Head } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";

// ==================== CONFIGURACIÓN DE ESTADOS ====================
const estadosConfig = {
    pendiente: {
        title: "Estimaciones Pendientes",
        description: "Estimaciones pendientes de revisión o aprobación",
        gradient: "from-blue-500 to-cyan-600",
        darkGradient: "dark:from-blue-900/30 dark:to-cyan-900/30",
        textColor: "text-blue-600 dark:text-blue-400",
        iconColor: "text-blue-700 dark:text-blue-300",
        iconBg: "bg-blue-100 dark:bg-blue-900/40",
        progressBg: "bg-gradient-to-r from-blue-500 to-cyan-600",
        badge: "bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300",
        icon: '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
        labelCount: "pendientes",
        emptyMessage: "No hay estimaciones pendientes",
    },
    en_proceso: {
        title: "Estimaciones en Proceso",
        description: "Estimaciones actualmente en revisión o desarrollo activo",
        gradient: "from-amber-500 to-orange-600",
        darkGradient: "dark:from-amber-900/30 dark:to-orange-900/30",
        textColor: "text-amber-600 dark:text-amber-400",
        iconColor: "text-amber-700 dark:text-amber-300",
        iconBg: "bg-amber-100 dark:bg-amber-900/40",
        progressBg: "bg-gradient-to-r from-amber-500 to-orange-600",
        badge: "bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300",
        icon: '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
        labelCount: "en proceso",
        emptyMessage: "No hay estimaciones en proceso",
    },
    completado: {
        title: "Estimaciones Completadas",
        description: "Proyectos finalizados y entregados exitosamente",
        gradient: "from-green-500 to-emerald-600",
        darkGradient: "dark:from-green-900/30 dark:to-emerald-900/30",
        textColor: "text-green-600 dark:text-green-400",
        iconColor: "text-green-700 dark:text-green-300",
        iconBg: "bg-green-100 dark:bg-green-900/40",
        progressBg: "bg-gradient-to-r from-green-500 to-emerald-600",
        badge: "bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300",
        icon: '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
        labelCount: "completadas",
        emptyMessage: "No hay estimaciones completadas",
    },
    no_aprobados: {
        title: "Estimaciones No Aprobadas",
        description: "Estimaciones rechazadas o que requieren revisión",
        gradient: "from-red-500 to-rose-600",
        darkGradient: "dark:from-red-900/30 dark:to-rose-900/30",
        textColor: "text-red-600 dark:text-red-400",
        iconColor: "text-red-700 dark:text-red-300",
        iconBg: "bg-red-100 dark:bg-red-900/40",
        progressBg: "bg-gradient-to-r from-red-500 to-rose-600",
        badge: "bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300",
        icon: '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>',
        labelCount: "no aprobadas",
        emptyMessage: "No hay estimaciones rechazadas",
    },
    aprobado: {
        title: "Estimaciones Aprobadas",
        description: "Estimaciones finalizadas y validadas correctamente",
        gradient: "from-emerald-500 to-green-600",
        darkGradient: "dark:from-emerald-900/30 dark:to-green-900/30",
        textColor: "text-emerald-600 dark:text-emerald-400",
        iconColor: "text-emerald-700 dark:text-emerald-300",
        iconBg: "bg-emerald-100 dark:bg-emerald-900/40",
        progressBg: "bg-gradient-to-r from-emerald-500 to-green-600",
        badge: "bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300",
        icon: '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
        labelCount: "aprobadas",
        emptyMessage: "No hay estimaciones aprobadas",
    },
};

// ==================== ESTADO REACTIVO ====================
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

// Estado para almacenar estadísticas por estado
const estadoStats = ref({});

// ==================== CACHE PARA RENDIMIENTO ====================
const cache = ref({
    data: null,
    timestamp: null,
    TTL: 300000, // 5 minutos
});

// ==================== VIRTUAL SCROLLING ====================
const visibleEstimacionesCount = ref(6);
const visibleEstimaciones = computed(() => {
    return lists.value.estimaciones.slice(0, visibleEstimacionesCount.value);
});

// ==================== SKELETON STATES ====================
const showSkeleton = ref(true);
const currentHover = ref(null);

// ==================== FUNCIÓN DE CARGA OPTIMIZADA ====================
const fetchDashboard = async () => {
    try {
        // Verificar cache primero
        const now = Date.now();
        if (
            cache.value.data &&
            cache.value.timestamp &&
            now - cache.value.timestamp < cache.value.TTL
        ) {
            useCachedData();
            setTimeout(() => updateInBackground(), 1000);
            return;
        }

        // Mostrar skeleton inmediatamente
        showSkeleton.value = true;

        // Cargar datos en paralelo con timeout
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 10000);

        const [estimacionesRes, tareasRes, integracionesRes, fasesRes] =
            await Promise.all([
                axios.get("/api/lists/estimaciones", {
                    signal: controller.signal,
                    timeout: 10000,
                }),
                axios.get("/api/lists/tareas", {
                    signal: controller.signal,
                    timeout: 10000,
                }),
                axios.get("/api/lists/integraciones", {
                    signal: controller.signal,
                    timeout: 10000,
                }),
                axios.get("/api/lists/fases", {
                    signal: controller.signal,
                    timeout: 10000,
                }),
            ]);

        clearTimeout(timeoutId);
        processDashboardData(
            estimacionesRes.data,
            tareasRes.data,
            integracionesRes.data,
            fasesRes.data,
        );

        // Guardar en cache
        cache.value = {
            data: {
                estimaciones: estimacionesRes.data,
                tareas: tareasRes.data,
                integraciones: integracionesRes.data,
                fases: fasesRes.data,
            },
            timestamp: Date.now(),
            TTL: cache.value.TTL,
        };
    } catch (error) {
        if (error.name === "AbortError" || error.code === "ECONNABORTED") {
            console.warn("Request timeout, using cached data if available");
        } else {
            console.error("Error loading dashboard:", error);
        }
        if (cache.value.data) useCachedData();
    } finally {
        setTimeout(() => {
            showSkeleton.value = false;
            loading.value = false;
        }, 300);
    }
};

// ==================== PROCESAMIENTO DE DATOS ====================
const processDashboardData = (estimaciones, tareas, integraciones, fases) => {
    // Calcular stats principales
    stats.value = {
        estimaciones: estimaciones.length,
        horas: estimaciones.reduce(
            (sum, e) => sum + Number(e.total_horas ?? 0),
            0,
        ),
        tareas: tareas.length,
        integraciones: integraciones.length,
    };

    // Calcular estadísticas por estado
    const newEstadoStats = {};
    Object.keys(estadosConfig).forEach((estadoKey) => {
        // Normalizar el estado de la API
        const filtered = estimaciones.filter((e) => {
            const estadoNormalizado = (e.estado || "")
                .toLowerCase()
                .replace(/\s+/g, "_");
            return estadoNormalizado === estadoKey;
        });

        newEstadoStats[estadoKey] = {
            count: filtered.length,
            horas: filtered.reduce(
                (sum, e) => sum + Number(e.total_horas || 0),
                0,
            ),
        };
    });
    estadoStats.value = newEstadoStats;

    // Limitar datos para rendimiento
    lists.value = {
        estimaciones: estimaciones.slice(0, 50),
        tareas: tareas.slice(0, 10),
        fases: fases.slice(0, 10),
    };
};

// ==================== USAR DATOS CACHEADOS ====================
const useCachedData = () => {
    const cached = cache.value.data;
    processDashboardData(
        cached.estimaciones,
        cached.tareas,
        cached.integraciones,
        cached.fases,
    );
};

// ==================== ACTUALIZACIÓN EN BACKGROUND ====================
const updateInBackground = () => {
    Promise.allSettled([
        axios
            .get("/api/lists/estimaciones", { timeout: 10000 })
            .catch(() => null),
        axios.get("/api/lists/tareas", { timeout: 10000 }).catch(() => null),
        axios
            .get("/api/lists/integraciones", { timeout: 10000 })
            .catch(() => null),
        axios.get("/api/lists/fases", { timeout: 10000 }).catch(() => null),
    ]).then(([est, tar, int, fas]) => {
        if (
            est?.status === 200 &&
            tar?.status === 200 &&
            int?.status === 200 &&
            fas?.status === 200
        ) {
            cache.value = {
                data: {
                    estimaciones: est.value.data,
                    tareas: tar.value.data,
                    integraciones: int.value.data,
                    fases: fas.value.data,
                },
                timestamp: Date.now(),
                TTL: cache.value.TTL,
            };
            processDashboardData(
                est.value.data,
                tar.value.data,
                int.value.data,
                fas.value.data,
            );
        }
    });
};

// ==================== CARGAR MÁS ESTIMACIONES ====================
const loadMoreEstimaciones = () => {
    visibleEstimacionesCount.value = Math.min(
        visibleEstimacionesCount.value + 6,
        lists.value.estimaciones.length,
    );
};

// ==================== FORMATEAR FECHA ====================
const formatDate = (date) => {
    if (!date) return "-";
    return new Date(date).toLocaleDateString("es-ES", {
        year: "numeric",
        month: "short",
        day: "numeric",
    });
};

// ==================== OBTENER CONFIGURACIÓN DE ESTADO ====================
const getEstadoConfig = (estadoKey) => {
    return estadosConfig[estadoKey] || estadosConfig.pendiente;
};

// ==================== OBTENER CLASE DE BADGE POR ESTADO ====================
const getEstadoBadgeClass = (estado) => {
    const estadoLower = (estado || "").toLowerCase().replace(/\s+/g, "_");
    if (estadoLower === "aprobado" || estadoLower === "aprobada") {
        return "bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300";
    }
    if (estadoLower === "en_proceso" || estadoLower === "proceso") {
        return "bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300";
    }
    if (estadoLower === "pendiente") {
        return "bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300";
    }
    if (estadoLower === "completado" || estadoLower === "finalizado") {
        return "bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300";
    }
    if (estadoLower === "no_aprobados" || estadoLower === "rechazado") {
        return "bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300";
    }
    return "bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300";
};

// ==================== MONTAR COMPONENTE ====================
onMounted(() => {
    fetchDashboard();

    const interval = setInterval(() => {
        if (!loading.value) updateInBackground();
    }, 300000);

    nextTick(() => {
        showSkeleton.value = true;
    });

    return () => clearInterval(interval);
});
</script>

<template>
    <Head title="Dashboard - Panel de Control" />
    <AppLayout title="Dashboard">
        <!-- ==================== SECCIÓN 1: SKELETON LOADER ==================== -->
        <div
            v-if="showSkeleton && !loading"
            class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
        >
            <!-- Stats Cards Skeleton -->
            <div
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8"
            >
                <div
                    v-for="i in 4"
                    :key="i"
                    class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 animate-pulse"
                >
                    <div
                        class="h-4 w-32 bg-gray-200 dark:bg-gray-700 rounded mb-4"
                    ></div>
                    <div
                        class="h-12 w-16 bg-gray-200 dark:bg-gray-700 rounded"
                    ></div>
                </div>
            </div>

            <!-- Secciones por Estado Skeleton -->
            <div
                class="mb-8 animate-pulse"
                v-for="estado in Object.keys(estadosConfig)"
                :key="estado"
            >
                <div
                    class="h-8 w-64 bg-gray-200 dark:bg-gray-700 rounded-lg mb-4"
                ></div>
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 h-96"
                ></div>
            </div>

            <!-- Acceso Rápido Skeleton -->
            <div class="mb-8 animate-pulse">
                <div
                    class="h-8 w-40 bg-gray-200 dark:bg-gray-700 rounded-lg mb-4"
                ></div>
                <div
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
                >
                    <div
                        v-for="i in 6"
                        :key="i"
                        class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 h-48"
                    ></div>
                </div>
            </div>
        </div>

        <!-- ==================== SECCIÓN 2: CONTENIDO REAL ==================== -->
        <div
            v-else
            class="min-h-screen bg-gradient-to-br from-gray-50 via-gray-100 to-gray-200 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900"
        >
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <!-- ==================== SUB-SECCIÓN 2.1: HEADER ==================== -->
                <div class="mb-10 animate-fade-in">
                    <div
                        class="relative bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl p-8 border border-gray-200/50 dark:bg-gray-800/80 dark:border-gray-700/50"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <h1
                                    class="text-4xl md:text-5xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500 animate-gradient-shift"
                                >
                                    📊 Dashboard
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
                                class="hidden md:block px-4 py-2 rounded-lg bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-medium shadow-lg"
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

                <!-- ==================== SUB-SECCIÓN 2.2: STATS CARDS (4 TARJETAS) ==================== -->
                <div
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 animate-fade-in-up"
                >
                    <!-- Card 1: Estimaciones Totales -->
                    <div
                        class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-50 to-blue-50 dark:from-indigo-900/30 dark:to-blue-900/30 border border-gray-200/50 dark:border-gray-700/50 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1"
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
                                        class="text-5xl font-extrabold text-indigo-600 dark:text-indigo-400 group-hover:scale-110 transition-transform duration-300"
                                    >
                                        {{ stats.estimaciones }}
                                    </p>
                                </div>
                                <div
                                    class="p-3 rounded-xl bg-indigo-100 dark:bg-indigo-900/30"
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
                                class="mt-5 h-2 w-full bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden"
                            >
                                <div
                                    class="h-full bg-gradient-to-r from-indigo-500 to-blue-500 rounded-full transition-all duration-700"
                                    :style="{
                                        width: `${Math.min(stats.estimaciones * 2, 100)}%`,
                                    }"
                                ></div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Horas Totales -->
                    <div
                        class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/30 dark:to-emerald-900/30 border border-gray-200/50 dark:border-gray-700/50 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1"
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
                                        class="text-5xl font-extrabold text-green-600 dark:text-green-400 group-hover:scale-110 transition-transform duration-300"
                                    >
                                        {{ stats.horas }}
                                    </p>
                                </div>
                                <div
                                    class="p-3 rounded-xl bg-green-100 dark:bg-green-900/30"
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
                                class="mt-5 h-2 w-full bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden"
                            >
                                <div
                                    class="h-full bg-gradient-to-r from-green-500 to-emerald-500 rounded-full transition-all duration-700"
                                    :style="{
                                        width: `${Math.min(stats.horas / 10, 100)}%`,
                                    }"
                                ></div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 3: Tareas -->
                    <div
                        class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-50 to-cyan-50 dark:from-blue-900/30 dark:to-cyan-900/30 border border-gray-200/50 dark:border-gray-700/50 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1"
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
                                        class="text-5xl font-extrabold text-blue-600 dark:text-blue-400 group-hover:scale-110 transition-transform duration-300"
                                    >
                                        {{ stats.tareas }}
                                    </p>
                                </div>
                                <div
                                    class="p-3 rounded-xl bg-blue-100 dark:bg-blue-900/30"
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
                                class="mt-5 h-2 w-full bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden"
                            >
                                <div
                                    class="h-full bg-gradient-to-r from-blue-500 to-cyan-500 rounded-full transition-all duration-700"
                                    :style="{
                                        width: `${Math.min(stats.tareas * 3, 100)}%`,
                                    }"
                                ></div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 4: Integraciones -->
                    <div
                        class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/30 dark:to-pink-900/30 border border-gray-200/50 dark:border-gray-700/50 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1"
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
                                        class="text-5xl font-extrabold text-purple-600 dark:text-purple-400 group-hover:scale-110 transition-transform duration-300"
                                    >
                                        {{ stats.integraciones }}
                                    </p>
                                </div>
                                <div
                                    class="p-3 rounded-xl bg-purple-100 dark:bg-purple-900/30"
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
                                class="mt-5 h-2 w-full bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden"
                            >
                                <div
                                    class="h-full bg-gradient-to-r from-purple-500 to-pink-500 rounded-full transition-all duration-700"
                                    :style="{
                                        width: `${Math.min(stats.integraciones * 5, 100)}%`,
                                    }"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ==================== SUB-SECCIÓN 2.3: ESTADO PENDIENTE ==================== -->
                <div
                    v-if="estadoStats.pendiente?.count > 0"
                    class="mt-12 animate-fade-in-up delay-100"
                >
                    <div
                        class="mb-6 animate-fade-in before:absolute before:-inset-1 before:rounded-2xl before:bg-gradient-to-r before:from-blue-500 before:to-cyan-600 before:opacity-0 before:blur-sm hover:before:opacity-10 transition-all duration-300 relative"
                    >
                        <div
                            class="relative bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl p-8 border border-gray-200/50 dark:bg-gray-800/80 dark:border-gray-700/50"
                        >
                            <div class="flex items-center justify-between">
                                <div>
                                    <h2
                                        class="text-3xl md:text-4xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 via-cyan-600 to-teal-500 animate-gradient-shift"
                                    >
                                        ⏳
                                        {{ getEstadoConfig("pendiente").title }}
                                    </h2>
                                    <p
                                        class="text-gray-600 dark:text-gray-300 mt-2 flex items-center space-x-2"
                                    >
                                        <span
                                            class="h-1.5 w-1.5 rounded-full bg-blue-500 animate-pulse"
                                        ></span>
                                        <span>{{
                                            getEstadoConfig("pendiente")
                                                .description
                                        }}</span>
                                    </p>
                                </div>
                                <div
                                    class="hidden md:flex items-center space-x-4"
                                >
                                    <div
                                        class="flex items-center px-4 py-2 rounded-lg bg-gradient-to-r from-blue-500 to-cyan-600 text-white font-medium shadow-lg"
                                    >
                                        <span class="font-bold">{{
                                            estadoStats.pendiente.count
                                        }}</span>
                                        <span class="ml-1">{{
                                            getEstadoConfig("pendiente")
                                                .labelCount
                                        }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden"
                    >
                        <div
                            class="px-6 py-5 border-b border-gray-200 dark:border-gray-700"
                        >
                            <h3
                                class="text-xl font-bold text-gray-800 dark:text-white flex items-center space-x-2"
                            >
                                <span>Lista de Estimaciones Pendientes</span>
                                <span class="text-sm font-normal text-gray-500"
                                    >({{ estadoStats.pendiente.count }})</span
                                >
                            </h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead
                                    class="text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/50"
                                >
                                    <tr
                                        class="border-b border-gray-200 dark:border-gray-700"
                                    >
                                        <th
                                            class="text-left py-3 px-4 font-medium"
                                        >
                                            Empresa
                                        </th>
                                        <th
                                            class="text-left py-3 px-4 font-medium"
                                        >
                                            Horas
                                        </th>
                                        <th
                                            class="text-left py-3 px-4 font-medium"
                                        >
                                            Fecha
                                        </th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="divide-y divide-gray-100 dark:divide-gray-700"
                                >
                                    <tr
                                        v-for="e in lists.estimaciones.filter(
                                            (est) =>
                                                (est.estado || '')
                                                    .toLowerCase()
                                                    .replace(/\s+/g, '_') ===
                                                'pendiente',
                                        )"
                                        :key="e.id"
                                        class="hover:bg-blue-50/50 dark:hover:bg-blue-900/20 transition-colors"
                                    >
                                        <td
                                            class="py-3 px-4 font-medium text-gray-800 dark:text-white"
                                        >
                                            {{ e.nombre_empresa }}
                                        </td>
                                        <td
                                            class="py-3 px-4 font-bold text-blue-600 dark:text-blue-400"
                                        >
                                            {{ e.total_horas ?? 0 }}
                                        </td>
                                        <td
                                            class="py-3 px-4 text-gray-500 dark:text-gray-400 text-sm"
                                        >
                                            {{ formatDate(e.created_at) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ==================== SUB-SECCIÓN 2.4: ESTADO EN PROCESO ==================== -->
                <div
                    v-if="estadoStats.en_proceso?.count > 0"
                    class="mt-12 animate-fade-in-up delay-200"
                >
                    <div
                        class="mb-6 animate-fade-in before:absolute before:-inset-1 before:rounded-2xl before:bg-gradient-to-r before:from-amber-500 before:to-orange-600 before:opacity-0 before:blur-sm hover:before:opacity-10 transition-all duration-300 relative"
                    >
                        <div
                            class="relative bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl p-8 border border-gray-200/50 dark:bg-gray-800/80 dark:border-gray-700/50"
                        >
                            <div class="flex items-center justify-between">
                                <div>
                                    <h2
                                        class="text-3xl md:text-4xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-amber-600 via-orange-600 to-yellow-500 animate-gradient-shift"
                                    >
                                        🔄
                                        {{
                                            getEstadoConfig("en_proceso").title
                                        }}
                                    </h2>
                                    <p
                                        class="text-gray-600 dark:text-gray-300 mt-2 flex items-center space-x-2"
                                    >
                                        <span
                                            class="h-1.5 w-1.5 rounded-full bg-amber-500 animate-pulse"
                                        ></span>
                                        <span>{{
                                            getEstadoConfig("en_proceso")
                                                .description
                                        }}</span>
                                    </p>
                                </div>
                                <div
                                    class="hidden md:flex items-center space-x-4"
                                >
                                    <div
                                        class="flex items-center px-4 py-2 rounded-lg bg-gradient-to-r from-amber-500 to-orange-600 text-white font-medium shadow-lg"
                                    >
                                        <span class="font-bold">{{
                                            estadoStats.en_proceso.count
                                        }}</span>
                                        <span class="ml-1">{{
                                            getEstadoConfig("en_proceso")
                                                .labelCount
                                        }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden"
                    >
                        <div
                            class="px-6 py-5 border-b border-gray-200 dark:border-gray-700"
                        >
                            <h3
                                class="text-xl font-bold text-gray-800 dark:text-white flex items-center space-x-2"
                            >
                                <span>Lista de Estimaciones en Proceso</span>
                                <span class="text-sm font-normal text-gray-500"
                                    >({{ estadoStats.en_proceso.count }})</span
                                >
                            </h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead
                                    class="text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/50"
                                >
                                    <tr
                                        class="border-b border-gray-200 dark:border-gray-700"
                                    >
                                        <th
                                            class="text-left py-3 px-4 font-medium"
                                        >
                                            Empresa
                                        </th>
                                        <th
                                            class="text-left py-3 px-4 font-medium"
                                        >
                                            Horas
                                        </th>
                                        <th
                                            class="text-left py-3 px-4 font-medium"
                                        >
                                            Fecha
                                        </th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="divide-y divide-gray-100 dark:divide-gray-700"
                                >
                                    <tr
                                        v-for="e in lists.estimaciones.filter(
                                            (est) =>
                                                (est.estado || '')
                                                    .toLowerCase()
                                                    .replace(/\s+/g, '_') ===
                                                'en_proceso',
                                        )"
                                        :key="e.id"
                                        class="hover:bg-amber-50/50 dark:hover:bg-amber-900/20 transition-colors"
                                    >
                                        <td
                                            class="py-3 px-4 font-medium text-gray-800 dark:text-white"
                                        >
                                            {{ e.nombre_empresa }}
                                        </td>
                                        <td
                                            class="py-3 px-4 font-bold text-amber-600 dark:text-amber-400"
                                        >
                                            {{ e.total_horas ?? 0 }}
                                        </td>
                                        <td
                                            class="py-3 px-4 text-gray-500 dark:text-gray-400 text-sm"
                                        >
                                            {{ formatDate(e.created_at) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ==================== SUB-SECCIÓN 2.5: ESTADO COMPLETADO ==================== -->
                <div
                    v-if="estadoStats.completado?.count > 0"
                    class="mt-12 animate-fade-in-up delay-300"
                >
                    <div
                        class="mb-6 animate-fade-in before:absolute before:-inset-1 before:rounded-2xl before:bg-gradient-to-r before:from-green-500 before:to-emerald-600 before:opacity-0 before:blur-sm hover:before:opacity-10 transition-all duration-300 relative"
                    >
                        <div
                            class="relative bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl p-8 border border-gray-200/50 dark:bg-gray-800/80 dark:border-gray-700/50"
                        >
                            <div class="flex items-center justify-between">
                                <div>
                                    <h2
                                        class="text-3xl md:text-4xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-green-600 via-emerald-600 to-teal-500 animate-gradient-shift"
                                    >
                                        ✅
                                        {{
                                            getEstadoConfig("completado").title
                                        }}
                                    </h2>
                                    <p
                                        class="text-gray-600 dark:text-gray-300 mt-2 flex items-center space-x-2"
                                    >
                                        <span
                                            class="h-1.5 w-1.5 rounded-full bg-green-500 animate-pulse"
                                        ></span>
                                        <span>{{
                                            getEstadoConfig("completado")
                                                .description
                                        }}</span>
                                    </p>
                                </div>
                                <div
                                    class="hidden md:flex items-center space-x-4"
                                >
                                    <div
                                        class="flex items-center px-4 py-2 rounded-lg bg-gradient-to-r from-green-500 to-emerald-600 text-white font-medium shadow-lg"
                                    >
                                        <span class="font-bold">{{
                                            estadoStats.completado.count
                                        }}</span>
                                        <span class="ml-1">{{
                                            getEstadoConfig("completado")
                                                .labelCount
                                        }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden"
                    >
                        <div
                            class="px-6 py-5 border-b border-gray-200 dark:border-gray-700"
                        >
                            <h3
                                class="text-xl font-bold text-gray-800 dark:text-white flex items-center space-x-2"
                            >
                                <span>Lista de Estimaciones Completadas</span>
                                <span class="text-sm font-normal text-gray-500"
                                    >({{ estadoStats.completado.count }})</span
                                >
                            </h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead
                                    class="text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/50"
                                >
                                    <tr
                                        class="border-b border-gray-200 dark:border-gray-700"
                                    >
                                        <th
                                            class="text-left py-3 px-4 font-medium"
                                        >
                                            Empresa
                                        </th>
                                        <th
                                            class="text-left py-3 px-4 font-medium"
                                        >
                                            Horas
                                        </th>
                                        <th
                                            class="text-left py-3 px-4 font-medium"
                                        >
                                            Fecha
                                        </th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="divide-y divide-gray-100 dark:divide-gray-700"
                                >
                                    <tr
                                        v-for="e in lists.estimaciones.filter(
                                            (est) =>
                                                (est.estado || '')
                                                    .toLowerCase()
                                                    .replace(/\s+/g, '_') ===
                                                'completado',
                                        )"
                                        :key="e.id"
                                        class="hover:bg-green-50/50 dark:hover:bg-green-900/20 transition-colors"
                                    >
                                        <td
                                            class="py-3 px-4 font-medium text-gray-800 dark:text-white"
                                        >
                                            {{ e.nombre_empresa }}
                                        </td>
                                        <td
                                            class="py-3 px-4 font-bold text-green-600 dark:text-green-400"
                                        >
                                            {{ e.total_horas ?? 0 }}
                                        </td>
                                        <td
                                            class="py-3 px-4 text-gray-500 dark:text-gray-400 text-sm"
                                        >
                                            {{ formatDate(e.created_at) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ==================== SUB-SECCIÓN 2.6: ESTADO NO APROBADOS ==================== -->
                <div
                    v-if="estadoStats.no_aprobados?.count > 0"
                    class="mt-12 animate-fade-in-up delay-400"
                >
                    <div
                        class="mb-6 animate-fade-in before:absolute before:-inset-1 before:rounded-2xl before:bg-gradient-to-r before:from-red-500 before:to-rose-600 before:opacity-0 before:blur-sm hover:before:opacity-10 transition-all duration-300 relative"
                    >
                        <div
                            class="relative bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl p-8 border border-gray-200/50 dark:bg-gray-800/80 dark:border-gray-700/50"
                        >
                            <div class="flex items-center justify-between">
                                <div>
                                    <h2
                                        class="text-3xl md:text-4xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-red-600 via-rose-600 to-pink-500 animate-gradient-shift"
                                    >
                                        ❌
                                        {{
                                            getEstadoConfig("no_aprobados")
                                                .title
                                        }}
                                    </h2>
                                    <p
                                        class="text-gray-600 dark:text-gray-300 mt-2 flex items-center space-x-2"
                                    >
                                        <span
                                            class="h-1.5 w-1.5 rounded-full bg-red-500 animate-pulse"
                                        ></span>
                                        <span>{{
                                            getEstadoConfig("no_aprobados")
                                                .description
                                        }}</span>
                                    </p>
                                </div>
                                <div
                                    class="hidden md:flex items-center space-x-4"
                                >
                                    <div
                                        class="flex items-center px-4 py-2 rounded-lg bg-gradient-to-r from-red-500 to-rose-600 text-white font-medium shadow-lg"
                                    >
                                        <span class="font-bold">{{
                                            estadoStats.no_aprobados.count
                                        }}</span>
                                        <span class="ml-1">{{
                                            getEstadoConfig("no_aprobados")
                                                .labelCount
                                        }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden"
                    >
                        <div
                            class="px-6 py-5 border-b border-gray-200 dark:border-gray-700"
                        >
                            <h3
                                class="text-xl font-bold text-gray-800 dark:text-white flex items-center space-x-2"
                            >
                                <span>Lista de Estimaciones No Aprobadas</span>
                                <span class="text-sm font-normal text-gray-500"
                                    >({{
                                        estadoStats.no_aprobados.count
                                    }})</span
                                >
                            </h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead
                                    class="text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/50"
                                >
                                    <tr
                                        class="border-b border-gray-200 dark:border-gray-700"
                                    >
                                        <th
                                            class="text-left py-3 px-4 font-medium"
                                        >
                                            Empresa
                                        </th>
                                        <th
                                            class="text-left py-3 px-4 font-medium"
                                        >
                                            Horas
                                        </th>
                                        <th
                                            class="text-left py-3 px-4 font-medium"
                                        >
                                            Fecha
                                        </th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="divide-y divide-gray-100 dark:divide-gray-700"
                                >
                                    <tr
                                        v-for="e in lists.estimaciones.filter(
                                            (est) =>
                                                (est.estado || '')
                                                    .toLowerCase()
                                                    .replace(/\s+/g, '_') ===
                                                'no_aprobados',
                                        )"
                                        :key="e.id"
                                        class="hover:bg-red-50/50 dark:hover:bg-red-900/20 transition-colors"
                                    >
                                        <td
                                            class="py-3 px-4 font-medium text-gray-800 dark:text-white"
                                        >
                                            {{ e.nombre_empresa }}
                                        </td>
                                        <td
                                            class="py-3 px-4 font-bold text-red-600 dark:text-red-400"
                                        >
                                            {{ e.total_horas ?? 0 }}
                                        </td>
                                        <td
                                            class="py-3 px-4 text-gray-500 dark:text-gray-400 text-sm"
                                        >
                                            {{ formatDate(e.created_at) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ==================== SUB-SECCIÓN 2.7: ESTADO APROBADO ==================== -->
                <div
                    v-if="estadoStats.aprobado?.count > 0"
                    class="mt-12 animate-fade-in-up delay-500"
                >
                    <div
                        class="mb-6 animate-fade-in before:absolute before:-inset-1 before:rounded-2xl before:bg-gradient-to-r before:from-emerald-500 before:to-green-600 before:opacity-0 before:blur-sm hover:before:opacity-10 transition-all duration-300 relative"
                    >
                        <div
                            class="relative bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl p-8 border border-gray-200/50 dark:bg-gray-800/80 dark:border-gray-700/50"
                        >
                            <div class="flex items-center justify-between">
                                <div>
                                    <h2
                                        class="text-3xl md:text-4xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-emerald-600 via-green-600 to-teal-500 animate-gradient-shift"
                                    >
                                        🎯
                                        {{ getEstadoConfig("aprobado").title }}
                                    </h2>
                                    <p
                                        class="text-gray-600 dark:text-gray-300 mt-2 flex items-center space-x-2"
                                    >
                                        <span
                                            class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"
                                        ></span>
                                        <span>{{
                                            getEstadoConfig("aprobado")
                                                .description
                                        }}</span>
                                    </p>
                                </div>
                                <div
                                    class="hidden md:flex items-center space-x-4"
                                >
                                    <div
                                        class="flex items-center px-4 py-2 rounded-lg bg-gradient-to-r from-emerald-500 to-green-600 text-white font-medium shadow-lg"
                                    >
                                        <span class="font-bold">{{
                                            estadoStats.aprobado.count
                                        }}</span>
                                        <span class="ml-1">{{
                                            getEstadoConfig("aprobado")
                                                .labelCount
                                        }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden"
                    >
                        <div
                            class="px-6 py-5 border-b border-gray-200 dark:border-gray-700"
                        >
                            <h3
                                class="text-xl font-bold text-gray-800 dark:text-white flex items-center space-x-2"
                            >
                                <span>Lista de Estimaciones Aprobadas</span>
                                <span class="text-sm font-normal text-gray-500"
                                    >({{ estadoStats.aprobado.count }})</span
                                >
                            </h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead
                                    class="text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/50"
                                >
                                    <tr
                                        class="border-b border-gray-200 dark:border-gray-700"
                                    >
                                        <th
                                            class="text-left py-3 px-4 font-medium"
                                        >
                                            Empresa
                                        </th>
                                        <th
                                            class="text-left py-3 px-4 font-medium"
                                        >
                                            Horas
                                        </th>
                                        <th
                                            class="text-left py-3 px-4 font-medium"
                                        >
                                            Fecha
                                        </th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="divide-y divide-gray-100 dark:divide-gray-700"
                                >
                                    <tr
                                        v-for="e in lists.estimaciones.filter(
                                            (est) =>
                                                (est.estado || '')
                                                    .toLowerCase()
                                                    .replace(/\s+/g, '_') ===
                                                'aprobado',
                                        )"
                                        :key="e.id"
                                        class="hover:bg-emerald-50/50 dark:hover:bg-emerald-900/20 transition-colors"
                                    >
                                        <td
                                            class="py-3 px-4 font-medium text-gray-800 dark:text-white"
                                        >
                                            {{ e.nombre_empresa }}
                                        </td>
                                        <td
                                            class="py-3 px-4 font-bold text-emerald-600 dark:text-emerald-400"
                                        >
                                            {{ e.total_horas ?? 0 }}
                                        </td>
                                        <td
                                            class="py-3 px-4 text-gray-500 dark:text-gray-400 text-sm"
                                        >
                                            {{ formatDate(e.created_at) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ==================== SUB-SECCIÓN 2.8: RESUMEN DE ESTADOS CON TASAS ==================== -->
                <div
                    class="mt-12 bg-gradient-to-r from-blue-500 to-amber-500 rounded-2xl p-6 shadow-xl relative overflow-hidden animate-fade-in-up delay-600"
                >
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-blue-600 to-amber-600 opacity-90"
                    ></div>
                    <div
                        class="absolute inset-0 bg-[radial-gradient(circle_at_30%_50%,rgba(255,255,255,0.1),transparent_40%)]"
                    ></div>
                    <div class="relative">
                        <div
                            class="flex flex-col md:flex-row md:items-center md:justify-between mb-5 pb-4 border-b border-white/20"
                        >
                            <div class="mb-3 md:mb-0">
                                <h3
                                    class="text-2xl md:text-3xl font-bold text-white mb-1 flex items-center space-x-3"
                                >
                                    <svg
                                        class="h-8 w-8"
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
                                    <span>Resumen de Estados</span>
                                </h3>
                                <p
                                    class="text-blue-100 text-sm md:text-base flex flex-wrap items-center"
                                >
                                    <span class="font-bold text-white"
                                        >Total:
                                        {{
                                            stats.estimaciones
                                        }}
                                        estimaciones</span
                                    >
                                    <span
                                        class="mx-2 text-blue-200 hidden md:inline"
                                        >•</span
                                    >
                                    <span
                                        class="flex items-center mt-1 md:mt-0"
                                    >
                                        <span
                                            class="h-2 w-2 rounded-full bg-white mr-2"
                                        ></span>
                                        <span class="text-blue-100"
                                            >Actualizado:
                                            {{
                                                new Date().toLocaleTimeString(
                                                    "es-ES",
                                                    {
                                                        hour: "2-digit",
                                                        minute: "2-digit",
                                                    },
                                                )
                                            }}</span
                                        >
                                    </span>
                                </p>
                            </div>
                            <div
                                class="flex items-center bg-white/10 backdrop-blur-sm px-4 py-2 rounded-xl border border-white/20"
                            >
                                <div
                                    class="w-3 h-3 rounded-full bg-emerald-400 mr-2 animate-pulse"
                                ></div>
                                <span class="text-white font-bold text-lg">
                                    {{
                                        estadoStats.aprobado
                                            ? (
                                                  (estadoStats.aprobado.count /
                                                      stats.estimaciones) *
                                                  100
                                              ).toFixed(0)
                                            : 0
                                    }}%
                                </span>
                                <span class="ml-2 text-white/90 text-sm"
                                    >Tasa de aprobación general</span
                                >
                            </div>
                        </div>

                        <!-- Tasas por Estado -->
                        <div
                            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3"
                        >
                            <div
                                v-for="(estadoKey, index) in Object.keys(
                                    estadosConfig,
                                )"
                                :key="index"
                                v-if="estadoStats[estadoKey]?.count > 0"
                                class="group relative bg-white/5 backdrop-blur-sm rounded-xl p-4 border border-white/10 hover:border-white/30 hover:bg-white/10 transition-all duration-300 overflow-hidden"
                            >
                                <!-- Decorative background element -->
                                <div
                                    class="absolute inset-0 opacity-5 group-hover:opacity-10 transition-opacity duration-300"
                                    :class="getEstadoConfig(estadoKey).gradient"
                                ></div>

                                <div
                                    class="relative flex items-start space-x-3"
                                >
                                    <div class="flex-shrink-0 mt-1">
                                        <div
                                            class="w-3 h-3 rounded-full"
                                            :class="
                                                getEstadoConfig(estadoKey)
                                                    .textColor
                                            "
                                        ></div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div
                                            class="flex items-start justify-between"
                                        >
                                            <div>
                                                <p
                                                    class="text-white font-bold text-lg"
                                                >
                                                    {{
                                                        estadoStats[estadoKey]
                                                            .count
                                                    }}
                                                </p>
                                                <p
                                                    class="text-white/80 text-xs mt-0.5"
                                                >
                                                    {{
                                                        getEstadoConfig(
                                                            estadoKey,
                                                        ).labelCount
                                                    }}
                                                </p>
                                            </div>
                                            <div
                                                class="px-2.5 py-1 rounded-full text-xs font-bold"
                                                :class="
                                                    getEstadoConfig(estadoKey)
                                                        .badge
                                                "
                                            >
                                                {{
                                                    stats.estimaciones > 0
                                                        ? Math.round(
                                                              (estadoStats[
                                                                  estadoKey
                                                              ].count /
                                                                  stats.estimaciones) *
                                                                  100,
                                                          )
                                                        : 0
                                                }}%
                                            </div>
                                        </div>

                                        <div class="mt-3">
                                            <div
                                                class="flex justify-between text-xs text-white/70 mb-1"
                                            >
                                                <span>Tasa</span>
                                                <span
                                                    >{{
                                                        stats.estimaciones > 0
                                                            ? Math.round(
                                                                  (estadoStats[
                                                                      estadoKey
                                                                  ].count /
                                                                      stats.estimaciones) *
                                                                      100,
                                                              )
                                                            : 0
                                                    }}%</span
                                                >
                                            </div>
                                            <div
                                                class="h-1.5 w-full bg-white/10 rounded-full overflow-hidden"
                                            >
                                                <div
                                                    class="h-full rounded-full transition-all duration-700"
                                                    :class="
                                                        getEstadoConfig(
                                                            estadoKey,
                                                        ).progressBg
                                                    "
                                                    :style="{
                                                        width: `${stats.estimaciones > 0 ? Math.min((estadoStats[estadoKey].count / stats.estimaciones) * 100, 100) : 0}%`,
                                                    }"
                                                ></div>
                                            </div>
                                        </div>

                                        <div
                                            class="mt-2 pt-2 border-t border-white/10"
                                        >
                                            <div
                                                class="flex justify-between text-xs"
                                            >
                                                <span class="text-white/70"
                                                    >Horas totales</span
                                                >
                                                <span
                                                    class="font-bold text-white"
                                                    >{{
                                                        estadoStats[estadoKey]
                                                            .horas
                                                    }}</span
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Decorative corner element -->
                                <div
                                    class="absolute -top-2 -right-2 h-8 w-8 rounded-full opacity-10"
                                    :class="
                                        getEstadoConfig(
                                            estadoKey,
                                        ).textColor.replace('text-', 'bg-')
                                    "
                                ></div>
                            </div>
                        </div>

                        <!-- Leyenda de Tasas -->
                        <div class="mt-5 pt-4 border-t border-white/15">
                            <div
                                class="flex flex-wrap items-center justify-center md:justify-end space-x-4 text-white/80 text-sm"
                            >
                                <div class="flex items-center">
                                    <div
                                        class="w-2 h-2 rounded-full bg-emerald-400 mr-1.5"
                                    ></div>
                                    <span
                                        >Aprobado:
                                        {{
                                            estadoStats.aprobado
                                                ? (
                                                      (estadoStats.aprobado
                                                          .count /
                                                          stats.estimaciones) *
                                                      100
                                                  ).toFixed(1)
                                                : 0
                                        }}%</span
                                    >
                                </div>
                                <div class="flex items-center">
                                    <div
                                        class="w-2 h-2 rounded-full bg-amber-400 mr-1.5"
                                    ></div>
                                    <span
                                        >En Proceso:
                                        {{
                                            estadoStats.en_proceso
                                                ? (
                                                      (estadoStats.en_proceso
                                                          .count /
                                                          stats.estimaciones) *
                                                      100
                                                  ).toFixed(1)
                                                : 0
                                        }}%</span
                                    >
                                </div>
                                <div class="flex items-center">
                                    <div
                                        class="w-2 h-2 rounded-full bg-blue-400 mr-1.5"
                                    ></div>
                                    <span
                                        >Pendiente:
                                        {{
                                            estadoStats.pendiente
                                                ? (
                                                      (estadoStats.pendiente
                                                          .count /
                                                          stats.estimaciones) *
                                                      100
                                                  ).toFixed(1)
                                                : 0
                                        }}%</span
                                    >
                                </div>
                                <div class="flex items-center">
                                    <div
                                        class="w-2 h-2 rounded-full bg-green-400 mr-1.5"
                                    ></div>
                                    <span
                                        >Completado:
                                        {{
                                            estadoStats.completado
                                                ? (
                                                      (estadoStats.completado
                                                          .count /
                                                          stats.estimaciones) *
                                                      100
                                                  ).toFixed(1)
                                                : 0
                                        }}%</span
                                    >
                                </div>
                                <div class="flex items-center">
                                    <div
                                        class="w-2 h-2 rounded-full bg-red-400 mr-1.5"
                                    ></div>
                                    <span
                                        >No Aprobado:
                                        {{
                                            estadoStats.no_aprobados
                                                ? (
                                                      (estadoStats.no_aprobados
                                                          .count /
                                                          stats.estimaciones) *
                                                      100
                                                  ).toFixed(1)
                                                : 0
                                        }}%</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ==================== SUB-SECCIÓN 2.3: ACCESO RÁPIDO ==================== -->
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
                        <!-- Card: Crear Estimación -->
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

                        <!-- Card: Lista de Estimaciones -->
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

                        <!-- Card: Niveles -->
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
                                                >COMPLEJIDAD</span
                                            >
                                        </div>
                                        <h3
                                            class="text-xl font-bold text-gray-800 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors duration-200"
                                        >
                                            Niveles de Complejidad
                                        </h3>
                                        <p
                                            class="mt-2 text-sm text-gray-600 dark:text-gray-300 group-hover:text-purple-700 dark:group-hover:text-purple-300 transition-colors duration-200"
                                        >
                                            Clasificación de niveles según
                                            dificultad, experiencia y alcance
                                            funcional.
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
                                    <span>Explorar niveles</span>
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

                        <!-- Card: Fases -->
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

                        <!-- Card: Integraciones -->
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

                        <!-- Card: Tipos de Implementación -->
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
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* ==================== ANIMACIONES ==================== */
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

@keyframes gradient-shift {
    0%,
    100% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
}

@keyframes pulse {
    0%,
    100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.animate-fade-in {
    animation: fade-in 0.4s ease-out forwards;
}
.animate-fade-in-up {
    animation: fade-in-up 0.4s ease-out forwards;
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
.animate-fade-in-up.delay-400 {
    animation-delay: 0.4s;
}
.animate-fade-in-up.delay-500 {
    animation-delay: 0.5s;
}
.animate-fade-in-up.delay-600 {
    animation-delay: 0.6s;
}
.animate-fade-in-up.delay-700 {
    animation-delay: 0.7s;
}
.animate-gradient-shift {
    animation: gradient-shift 3s ease infinite;
    background-size: 200% 200%;
}
.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* ==================== SCROLLBAR PERSONALIZADO ==================== */
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

/* ==================== TRANSICIONES ==================== */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
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
