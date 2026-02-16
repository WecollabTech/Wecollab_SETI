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
        icon: '<svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
        labelCount: "pendientes",
        emptyMessage: "No hay estimaciones pendientes",
        iconPath: "M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z",
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
        icon: '<svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
        labelCount: "en proceso",
        emptyMessage: "No hay estimaciones en proceso",
        iconPath: "M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z",
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
        icon: '<svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
        labelCount: "completadas",
        emptyMessage: "No hay estimaciones completadas",
        iconPath: "M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z",
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
        icon: '<svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>',
        labelCount: "no aprobadas",
        emptyMessage: "No hay estimaciones rechazadas",
        iconPath: "M6 18L18 6M6 6l12 12",
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
        icon: '<svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
        labelCount: "aprobadas",
        emptyMessage: "No hay estimaciones aprobadas",
        iconPath: "M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z",
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

// IMPORTANTE: Incluir integraciones en el estado inicial
const lists = ref({
    estimaciones: [],
    tareas: [],
    integraciones: [],
    fases: [],
});

// Estado para almacenar estadísticas por estado
const estadoStats = ref({});

// Estado para expandir/colapsar estimaciones
const expandedEstimaciones = ref([]);

// ==================== CACHE PARA RENDIMIENTO ====================
const cache = ref({
    data: null,
    timestamp: null,
    TTL: 300000, // 5 minutos
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
                axios.get("/api/lists/tareas-estimaciones", {
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
    // Asegurar que sean arrays
    const estimacionesArray = Array.isArray(estimaciones) ? estimaciones : [];
    const tareasArray = Array.isArray(tareas) ? tareas : [];
    const integracionesArray = Array.isArray(integraciones)
        ? integraciones
        : [];
    const fasesArray = Array.isArray(fases) ? fases : [];

    // Calcular stats principales (totales)
    stats.value = {
        estimaciones: estimacionesArray.length,
        horas: estimacionesArray.reduce(
            (sum, e) => sum + Number(e.total_horas ?? 0),
            0,
        ),
        tareas: tareasArray.length,
        integraciones: integracionesArray.length,
    };

    // Calcular estadísticas por estado
    const newEstadoStats = {};

    Object.keys(estadosConfig).forEach((estadoKey) => {
        // Filtrar estimaciones por estado
        const filteredEstimaciones = estimacionesArray.filter((e) => {
            const estadoNormalizado = (e.estado || "")
                .toLowerCase()
                .replace(/\s+/g, "_");
            return estadoNormalizado === estadoKey;
        });

        // Filtrar tareas asociadas solo a las estimaciones filtradas
        const estimacionIds = filteredEstimaciones.map((e) => e.id);
        const filteredTareas = tareasArray.filter((t) =>
            estimacionIds.includes(t.estimacion_id),
        );

        // Filtrar integraciones asociadas a estas estimaciones
        const filteredIntegraciones = integracionesArray.filter((i) =>
            estimacionIds.includes(i.estimacion_id),
        );

        newEstadoStats[estadoKey] = {
            count: filteredEstimaciones.length,
            horas: filteredEstimaciones.reduce(
                (sum, e) => sum + Number(e.total_horas || 0),
                0,
            ),
            tareas: filteredTareas.length,
            integraciones: filteredIntegraciones.length,
        };
    });

    estadoStats.value = newEstadoStats;

    // Enriquecer estimaciones con sus tareas e integraciones
    const estimacionesEnriquecidas = estimacionesArray.map((estimacion) => ({
        ...estimacion,
        tareas: tareasArray.filter((t) => t.estimacion_id === estimacion.id),
        integraciones: integracionesArray.filter(
            (i) => i.estimacion_id === estimacion.id,
        ),
    }));

    // Limitar listas para rendimiento
    lists.value = {
        estimaciones: estimacionesEnriquecidas.slice(0, 50),
        tareas: tareasArray.slice(0, 50),
        integraciones: integracionesArray.slice(0, 50),
        fases: fasesArray.slice(0, 10),
    };
};

// ==================== USAR DATOS CACHEADOS ====================
const useCachedData = () => {
    const cached = cache.value.data;
    if (cached) {
        processDashboardData(
            cached.estimaciones,
            cached.tareas,
            cached.integraciones,
            cached.fases,
        );
    }
};

// ==================== ACTUALIZACIÓN EN BACKGROUND ====================
const updateInBackground = () => {
    Promise.allSettled([
        axios
            .get("/api/lists/estimaciones", { timeout: 10000 })
            .catch(() => null),
        axios
            .get("/api/lists/tareas-estimaciones", { timeout: 10000 })
            .catch(() => null),
        axios
            .get("/api/lists/integraciones", { timeout: 10000 })
            .catch(() => null),
        axios.get("/api/lists/fases", { timeout: 10000 }).catch(() => null),
    ]).then(([est, tar, int, fas]) => {
        if (
            est?.value?.status === 200 &&
            tar?.value?.status === 200 &&
            int?.value?.status === 200 &&
            fas?.value?.status === 200
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

// ==================== TOGGLE PARA EXPANDIR ESTIMACIÓN ====================
const toggleEstimacion = (id) => {
    if (expandedEstimaciones.value.includes(id)) {
        expandedEstimaciones.value = expandedEstimaciones.value.filter(
            (estId) => estId !== id,
        );
    } else {
        expandedEstimaciones.value.push(id);
    }
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

// ==================== FILTRAR ESTIMACIONES POR ESTADO ====================
const estimacionesPorEstado = computed(() => {
    const resultado = {};
    Object.keys(estadosConfig).forEach((estadoKey) => {
        resultado[estadoKey] = lists.value.estimaciones.filter((e) => {
            const estadoNormalizado = (e.estado || "")
                .toLowerCase()
                .replace(/\s+/g, "_");
            return estadoNormalizado === estadoKey;
        });
    });
    return resultado;
});

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

                <!-- ==================== SUB-SECCIÓN 2.2: ESTADÍSTICAS GENERALES ==================== -->
                <div
                    v-if="stats.estimaciones > 0"
                    class="mt-12 animate-fade-in-up delay-200"
                >
                    <!-- Header de la sección -->
                    <div
                        class="mb-6 relative animate-fade-in before:absolute before:-inset-1 before:rounded-2xl before:bg-gradient-to-r before:from-yellow-400 before:to-amber-600 before:opacity-0 before:blur-md hover:before:opacity-20 transition-all duration-300"
                    >
                        <div
                            class="relative bg-white/90 dark:bg-gray-800/80 backdrop-blur-md rounded-3xl shadow-xl p-8 border border-gray-200/40 dark:border-gray-700/40"
                        >
                            <div
                                class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4"
                            >
                                <div class="flex flex-col gap-2">
                                    <h2
                                        class="text-3xl md:text-4xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-yellow-500 via-amber-500 to-orange-500 animate-gradient-shift"
                                    >
                                        Estadísticas Generales
                                    </h2>
                                    <p
                                        class="text-gray-600 dark:text-gray-300 flex items-center gap-2 text-sm md:text-base"
                                    >
                                        <span
                                            class="h-2 w-2 rounded-full bg-yellow-500 animate-pulse"
                                        ></span>
                                        <span
                                            >Resumen completo del sistema</span
                                        >
                                    </p>
                                </div>
                            </div>

                            <!-- ==================== Cards ==================== -->
                            <div
                                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mt-8"
                            >
                                <!-- Card 1: Estimaciones Totales -->
                                <div
                                    class="group relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-100 via-indigo-50 to-blue-50 dark:from-indigo-900/30 dark:via-indigo-800/30 dark:to-blue-900/30 border border-gray-200/40 dark:border-gray-700/40 shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-1"
                                >
                                    <div class="relative p-7">
                                        <div
                                            class="flex items-start justify-between"
                                        >
                                            <div>
                                                <p
                                                    class="text-sm font-medium text-gray-600 dark:text-gray-300 mb-2 flex items-center gap-2"
                                                >
                                                    <svg
                                                        class="h-5 w-5 text-indigo-500"
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
                                                    class="text-5xl md:text-6xl font-extrabold text-indigo-700 dark:text-indigo-400 group-hover:scale-110 transition-transform duration-300"
                                                >
                                                    {{ stats.estimaciones }}
                                                </p>
                                            </div>
                                            <div
                                                class="p-3 rounded-xl bg-indigo-200 dark:bg-indigo-900/20 group-hover:bg-indigo-300 transition-colors duration-300"
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
                                    </div>
                                </div>

                                <!-- Card 2: Horas Totales -->
                                <div
                                    class="group relative overflow-hidden rounded-3xl bg-gradient-to-br from-green-50 via-green-100 to-emerald-50 dark:from-green-900/30 dark:via-green-800/30 dark:to-emerald-900/30 border border-gray-200/40 dark:border-gray-700/40 shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-1"
                                >
                                    <div class="relative p-7">
                                        <div
                                            class="flex items-start justify-between"
                                        >
                                            <div>
                                                <p
                                                    class="text-sm font-medium text-gray-600 dark:text-gray-300 mb-2 flex items-center gap-2"
                                                >
                                                    <svg
                                                        class="h-5 w-5 text-green-500"
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
                                                    class="text-5xl md:text-6xl font-extrabold text-green-700 dark:text-green-400 group-hover:scale-110 transition-transform duration-300"
                                                >
                                                    {{ stats.horas }}
                                                </p>
                                            </div>
                                            <div
                                                class="p-3 rounded-xl bg-green-200 dark:bg-green-900/20 group-hover:bg-green-300 transition-colors duration-300"
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
                                    </div>
                                </div>

                                <!-- Card 3: Tareas de Estimaciones -->
                                <div
                                    class="group relative overflow-hidden rounded-3xl bg-gradient-to-br from-blue-50 via-blue-100 to-cyan-50 dark:from-blue-900/30 dark:via-blue-800/30 dark:to-cyan-900/30 border border-gray-200/40 dark:border-gray-700/40 shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-1"
                                >
                                    <div class="relative p-7">
                                        <div
                                            class="flex items-start justify-between"
                                        >
                                            <div>
                                                <p
                                                    class="text-sm font-medium text-gray-600 dark:text-gray-300 mb-2 flex items-center gap-2"
                                                >
                                                    <svg
                                                        class="h-5 w-5 text-blue-500"
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
                                                    <span
                                                        >Tareas de
                                                        Estimaciones</span
                                                    >
                                                </p>
                                                <p
                                                    class="text-5xl md:text-6xl font-extrabold text-blue-700 dark:text-blue-400 group-hover:scale-110 transition-transform duration-300"
                                                >
                                                    {{ stats.tareas }}
                                                </p>
                                            </div>
                                            <div
                                                class="p-3 rounded-xl bg-blue-200 dark:bg-blue-900/20 group-hover:bg-blue-300 transition-colors duration-300"
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
                                    </div>
                                </div>

                                <!-- Card 4: Integraciones de Estimaciones -->
                                <div
                                    class="group relative overflow-hidden rounded-3xl bg-gradient-to-br from-purple-50 via-pink-50 to-pink-100 dark:from-purple-900/30 dark:via-purple-800/30 dark:to-pink-900/30 border border-gray-200/40 dark:border-gray-700/40 shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-1"
                                >
                                    <div class="relative p-7">
                                        <div
                                            class="flex items-start justify-between"
                                        >
                                            <div>
                                                <p
                                                    class="text-sm font-medium text-gray-600 dark:text-gray-300 mb-2 flex items-center gap-2"
                                                >
                                                    <svg
                                                        class="h-5 w-5 text-purple-500"
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
                                                    class="text-5xl md:text-6xl font-extrabold text-purple-700 dark:text-purple-400 group-hover:scale-110 transition-transform duration-300"
                                                >
                                                    {{ stats.integraciones }}
                                                </p>
                                            </div>
                                            <div
                                                class="p-3 rounded-xl bg-purple-200 dark:bg-purple-900/20 group-hover:bg-purple-300 transition-colors duration-300"
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
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ==================== SECCIÓN DE ESTADOS CON ESTIMACIONES DETALLADAS ==================== -->

                <!-- ESTADO PENDIENTE -->
                <div
                    v-if="estimacionesPorEstado.pendiente?.length > 0"
                    class="mt-12 animate-fade-in-up delay-100"
                >
                    <div
                        class="mb-6 relative before:absolute before:-inset-1 before:rounded-2xl before:bg-gradient-to-r before:from-blue-500 before:to-cyan-600 before:opacity-0 before:blur-sm hover:before:opacity-10 transition-all duration-300"
                    >
                        <div
                            class="relative bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl p-8 border border-gray-200/50 dark:bg-gray-800/80 dark:border-gray-700/50"
                        >
                            <div
                                class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4"
                            >
                                <div class="flex flex-col gap-2">
                                    <h2
                                        class="text-3xl md:text-4xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 via-cyan-600 to-teal-500 animate-gradient-shift"
                                    >
                                        ⏳
                                        {{ getEstadoConfig("pendiente").title }}
                                    </h2>
                                    <p
                                        class="text-gray-600 dark:text-gray-300 flex items-center gap-2 text-sm md:text-base"
                                    >
                                        <span
                                            class="h-2 w-2 rounded-full bg-blue-500 animate-pulse"
                                        ></span>
                                        <span>{{
                                            getEstadoConfig("pendiente")
                                                .description
                                        }}</span>
                                    </p>
                                </div>
                                <div class="hidden md:flex items-center gap-4">
                                    <div
                                        class="flex items-center px-4 py-2 rounded-lg bg-gradient-to-r from-blue-500 to-cyan-600 text-white font-medium shadow-lg"
                                    >
                                        <span class="font-bold">{{
                                            estadoStats.pendiente?.count || 0
                                        }}</span>
                                        <span class="ml-1">{{
                                            getEstadoConfig("pendiente")
                                                .labelCount
                                        }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Cards de métricas -->
                            <div
                                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-6"
                            >
                                <div
                                    class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-xl transition-all duration-300 p-6 flex flex-col gap-2"
                                >
                                    <p
                                        class="text-sm font-medium text-gray-600 dark:text-gray-300"
                                    >
                                        Estimaciones
                                    </p>
                                    <p
                                        class="text-4xl font-extrabold text-gray-800 dark:text-white"
                                    >
                                        {{ estadoStats.pendiente?.count || 0 }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{
                                            stats.estimaciones > 0
                                                ? Math.round(
                                                      (estadoStats.pendiente
                                                          ?.count /
                                                          stats.estimaciones) *
                                                          100,
                                                  )
                                                : 0
                                        }}% del total
                                    </p>
                                    <div
                                        class="h-3 w-full bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden mt-2"
                                    >
                                        <div
                                            class="h-full bg-blue-500 rounded-full transition-all duration-700"
                                            :style="{
                                                width: `${stats.estimaciones > 0 ? Math.min((estadoStats.pendiente?.count / stats.estimaciones) * 100, 100) : 0}%`,
                                            }"
                                        ></div>
                                    </div>
                                </div>
                                <div
                                    class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-xl transition-all duration-300 p-6 flex flex-col gap-2"
                                >
                                    <p
                                        class="text-sm font-medium text-gray-600 dark:text-gray-300"
                                    >
                                        Horas
                                    </p>
                                    <p
                                        class="text-4xl font-extrabold text-gray-800 dark:text-white"
                                    >
                                        {{ estadoStats.pendiente?.horas || 0 }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{
                                            stats.horas > 0
                                                ? Math.round(
                                                      (estadoStats.pendiente
                                                          ?.horas /
                                                          stats.horas) *
                                                          100,
                                                  )
                                                : 0
                                        }}% del total
                                    </p>
                                    <div
                                        class="h-3 w-full bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden mt-2"
                                    >
                                        <div
                                            class="h-full bg-green-500 rounded-full transition-all duration-700"
                                            :style="{
                                                width: `${stats.horas > 0 ? Math.min((estadoStats.pendiente?.horas / stats.horas) * 100, 100) : 0}%`,
                                            }"
                                        ></div>
                                    </div>
                                </div>
                                <div
                                    class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-xl transition-all duration-300 p-6 flex flex-col gap-2"
                                >
                                    <p
                                        class="text-sm font-medium text-gray-600 dark:text-gray-300"
                                    >
                                        Tareas
                                    </p>
                                    <p
                                        class="text-4xl font-extrabold text-blue-600 dark:text-blue-400"
                                    >
                                        {{ estadoStats.pendiente?.tareas || 0 }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{
                                            stats.tareas > 0
                                                ? Math.round(
                                                      (estadoStats.pendiente
                                                          ?.tareas /
                                                          stats.tareas) *
                                                          100,
                                                  )
                                                : 0
                                        }}% del total
                                    </p>
                                    <div
                                        class="h-3 w-full bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden mt-2"
                                    >
                                        <div
                                            class="h-full bg-blue-500 rounded-full transition-all duration-700"
                                            :style="{
                                                width: `${stats.tareas > 0 ? Math.min((estadoStats.pendiente?.tareas / stats.tareas) * 100, 100) : 0}%`,
                                            }"
                                        ></div>
                                    </div>
                                </div>
                                <div
                                    class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-xl transition-all duration-300 p-6 flex flex-col gap-2"
                                >
                                    <p
                                        class="text-sm font-medium text-gray-600 dark:text-gray-300"
                                    >
                                        Integraciones
                                    </p>
                                    <p
                                        class="text-4xl font-extrabold text-purple-600 dark:text-purple-400"
                                    >
                                        {{
                                            estadoStats.pendiente
                                                ?.integraciones || 0
                                        }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{
                                            stats.integraciones > 0
                                                ? Math.round(
                                                      (estadoStats.pendiente
                                                          ?.integraciones /
                                                          stats.integraciones) *
                                                          100,
                                                  )
                                                : 0
                                        }}% del total
                                    </p>
                                    <div
                                        class="h-3 w-full bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden mt-2"
                                    >
                                        <div
                                            class="h-full bg-purple-500 rounded-full transition-all duration-700"
                                            :style="{
                                                width: `${stats.integraciones > 0 ? Math.min((estadoStats.pendiente?.integraciones / stats.integraciones) * 100, 100) : 0}%`,
                                            }"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ESTADO EN PROCESO -->
                <div
                    v-if="estimacionesPorEstado.en_proceso?.length > 0"
                    class="mt-12 animate-fade-in-up delay-200"
                >
                    <div
                        class="mb-6 relative before:absolute before:-inset-1 before:rounded-2xl before:bg-gradient-to-r before:from-amber-500 before:to-orange-600 before:opacity-0 before:blur-sm hover:before:opacity-10 transition-all duration-300"
                    >
                        <div
                            class="relative bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl p-8 border border-gray-200/50 dark:bg-gray-800/80 dark:border-gray-700/50"
                        >
                            <div
                                class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4"
                            >
                                <div class="flex flex-col gap-2">
                                    <h2
                                        class="text-3xl md:text-4xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-amber-600 via-orange-600 to-yellow-500 animate-gradient-shift"
                                    >
                                        🔄
                                        {{
                                            getEstadoConfig("en_proceso").title
                                        }}
                                    </h2>
                                    <p
                                        class="text-gray-600 dark:text-gray-300 flex items-center gap-2 text-sm md:text-base"
                                    >
                                        <span
                                            class="h-2 w-2 rounded-full bg-amber-500 animate-pulse"
                                        ></span>
                                        <span>{{
                                            getEstadoConfig("en_proceso")
                                                .description
                                        }}</span>
                                    </p>
                                </div>
                                <div class="hidden md:flex items-center gap-4">
                                    <div
                                        class="flex items-center px-4 py-2 rounded-lg bg-gradient-to-r from-amber-500 to-orange-600 text-white font-medium shadow-lg"
                                    >
                                        <span class="font-bold">{{
                                            estadoStats.en_proceso?.count || 0
                                        }}</span>
                                        <span class="ml-1">{{
                                            getEstadoConfig("en_proceso")
                                                .labelCount
                                        }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Cards de métricas -->
                            <div
                                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-6"
                            >
                                <div
                                    class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-xl transition-all duration-300 p-6 flex flex-col gap-2"
                                >
                                    <p
                                        class="text-sm font-medium text-gray-600 dark:text-gray-300"
                                    >
                                        Estimaciones
                                    </p>
                                    <p
                                        class="text-4xl font-extrabold text-gray-800 dark:text-white"
                                    >
                                        {{ estadoStats.en_proceso?.count || 0 }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{
                                            stats.estimaciones > 0
                                                ? Math.round(
                                                      (estadoStats.en_proceso
                                                          ?.count /
                                                          stats.estimaciones) *
                                                          100,
                                                  )
                                                : 0
                                        }}% del total
                                    </p>
                                    <div
                                        class="h-3 w-full bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden mt-2"
                                    >
                                        <div
                                            class="h-full bg-amber-500 rounded-full transition-all duration-700"
                                            :style="{
                                                width: `${stats.estimaciones > 0 ? Math.min((estadoStats.en_proceso?.count / stats.estimaciones) * 100, 100) : 0}%`,
                                            }"
                                        ></div>
                                    </div>
                                </div>
                                <div
                                    class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-xl transition-all duration-300 p-6 flex flex-col gap-2"
                                >
                                    <p
                                        class="text-sm font-medium text-gray-600 dark:text-gray-300"
                                    >
                                        Horas
                                    </p>
                                    <p
                                        class="text-4xl font-extrabold text-gray-800 dark:text-white"
                                    >
                                        {{ estadoStats.en_proceso?.horas || 0 }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{
                                            stats.horas > 0
                                                ? Math.round(
                                                      (estadoStats.en_proceso
                                                          ?.horas /
                                                          stats.horas) *
                                                          100,
                                                  )
                                                : 0
                                        }}% del total
                                    </p>
                                    <div
                                        class="h-3 w-full bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden mt-2"
                                    >
                                        <div
                                            class="h-full bg-green-500 rounded-full transition-all duration-700"
                                            :style="{
                                                width: `${stats.horas > 0 ? Math.min((estadoStats.en_proceso?.horas / stats.horas) * 100, 100) : 0}%`,
                                            }"
                                        ></div>
                                    </div>
                                </div>
                                <div
                                    class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-xl transition-all duration-300 p-6 flex flex-col gap-2"
                                >
                                    <p
                                        class="text-sm font-medium text-gray-600 dark:text-gray-300"
                                    >
                                        Tareas
                                    </p>
                                    <p
                                        class="text-4xl font-extrabold text-blue-600 dark:text-blue-400"
                                    >
                                        {{
                                            estadoStats.en_proceso?.tareas || 0
                                        }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{
                                            stats.tareas > 0
                                                ? Math.round(
                                                      (estadoStats.en_proceso
                                                          ?.tareas /
                                                          stats.tareas) *
                                                          100,
                                                  )
                                                : 0
                                        }}% del total
                                    </p>
                                    <div
                                        class="h-3 w-full bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden mt-2"
                                    >
                                        <div
                                            class="h-full bg-blue-500 rounded-full transition-all duration-700"
                                            :style="{
                                                width: `${stats.tareas > 0 ? Math.min((estadoStats.en_proceso?.tareas / stats.tareas) * 100, 100) : 0}%`,
                                            }"
                                        ></div>
                                    </div>
                                </div>
                                <div
                                    class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-xl transition-all duration-300 p-6 flex flex-col gap-2"
                                >
                                    <p
                                        class="text-sm font-medium text-gray-600 dark:text-gray-300"
                                    >
                                        Integraciones
                                    </p>
                                    <p
                                        class="text-4xl font-extrabold text-purple-600 dark:text-purple-400"
                                    >
                                        {{
                                            estadoStats.en_proceso
                                                ?.integraciones || 0
                                        }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{
                                            stats.integraciones > 0
                                                ? Math.round(
                                                      (estadoStats.en_proceso
                                                          ?.integraciones /
                                                          stats.integraciones) *
                                                          100,
                                                  )
                                                : 0
                                        }}% del total
                                    </p>
                                    <div
                                        class="h-3 w-full bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden mt-2"
                                    >
                                        <div
                                            class="h-full bg-purple-500 rounded-full transition-all duration-700"
                                            :style="{
                                                width: `${stats.integraciones > 0 ? Math.min((estadoStats.en_proceso?.integraciones / stats.integraciones) * 100, 100) : 0}%`,
                                            }"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ESTADO COMPLETADO -->
                <div
                    v-if="estimacionesPorEstado.completado?.length > 0"
                    class="mt-12 animate-fade-in-up delay-300"
                >
                    <div
                        class="mb-6 relative before:absolute before:-inset-1 before:rounded-2xl before:bg-gradient-to-r before:from-green-500 before:to-emerald-600 before:opacity-0 before:blur-sm hover:before:opacity-10 transition-all duration-300"
                    >
                        <div
                            class="relative bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl p-8 border border-gray-200/50 dark:bg-gray-800/80 dark:border-gray-700/50"
                        >
                            <div
                                class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4"
                            >
                                <div class="flex flex-col gap-2">
                                    <h2
                                        class="text-3xl md:text-4xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-green-600 via-lime-500 to-emerald-500 animate-gradient-shift"
                                    >
                                        ✅
                                        {{
                                            getEstadoConfig("completado").title
                                        }}
                                    </h2>
                                    <p
                                        class="text-gray-600 dark:text-gray-300 flex items-center gap-2 text-sm md:text-base"
                                    >
                                        <span
                                            class="h-2 w-2 rounded-full bg-green-500 animate-pulse"
                                        ></span>
                                        <span>{{
                                            getEstadoConfig("completado")
                                                .description
                                        }}</span>
                                    </p>
                                </div>
                                <div class="hidden md:flex items-center gap-4">
                                    <div
                                        class="flex items-center px-4 py-2 rounded-lg bg-gradient-to-r from-green-500 to-emerald-600 text-white font-medium shadow-lg"
                                    >
                                        <span class="font-bold">{{
                                            estadoStats.completado?.count || 0
                                        }}</span>
                                        <span class="ml-1">{{
                                            getEstadoConfig("completado")
                                                .labelCount
                                        }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Cards de métricas -->
                            <div
                                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-6"
                            >
                                <div
                                    class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-xl transition-all duration-300 p-6 flex flex-col gap-2"
                                >
                                    <p
                                        class="text-sm font-medium text-gray-600 dark:text-gray-300"
                                    >
                                        Estimaciones
                                    </p>
                                    <p
                                        class="text-4xl font-extrabold text-gray-800 dark:text-white"
                                    >
                                        {{ estadoStats.completado?.count || 0 }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{
                                            stats.estimaciones > 0
                                                ? Math.round(
                                                      (estadoStats.completado
                                                          ?.count /
                                                          stats.estimaciones) *
                                                          100,
                                                  )
                                                : 0
                                        }}% del total
                                    </p>
                                    <div
                                        class="h-3 w-full bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden mt-2"
                                    >
                                        <div
                                            class="h-full bg-green-500 rounded-full transition-all duration-700"
                                            :style="{
                                                width: `${stats.estimaciones > 0 ? Math.min((estadoStats.completado?.count / stats.estimaciones) * 100, 100) : 0}%`,
                                            }"
                                        ></div>
                                    </div>
                                </div>
                                <div
                                    class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-xl transition-all duration-300 p-6 flex flex-col gap-2"
                                >
                                    <p
                                        class="text-sm font-medium text-gray-600 dark:text-gray-300"
                                    >
                                        Horas
                                    </p>
                                    <p
                                        class="text-4xl font-extrabold text-gray-800 dark:text-white"
                                    >
                                        {{ estadoStats.completado?.horas || 0 }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{
                                            stats.horas > 0
                                                ? Math.round(
                                                      (estadoStats.completado
                                                          ?.horas /
                                                          stats.horas) *
                                                          100,
                                                  )
                                                : 0
                                        }}% del total
                                    </p>
                                    <div
                                        class="h-3 w-full bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden mt-2"
                                    >
                                        <div
                                            class="h-full bg-lime-500 rounded-full transition-all duration-700"
                                            :style="{
                                                width: `${stats.horas > 0 ? Math.min((estadoStats.completado?.horas / stats.horas) * 100, 100) : 0}%`,
                                            }"
                                        ></div>
                                    </div>
                                </div>
                                <div
                                    class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-xl transition-all duration-300 p-6 flex flex-col gap-2"
                                >
                                    <p
                                        class="text-sm font-medium text-gray-600 dark:text-gray-300"
                                    >
                                        Tareas
                                    </p>
                                    <p
                                        class="text-4xl font-extrabold text-green-600 dark:text-green-400"
                                    >
                                        {{
                                            estadoStats.completado?.tareas || 0
                                        }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{
                                            stats.tareas > 0
                                                ? Math.round(
                                                      (estadoStats.completado
                                                          ?.tareas /
                                                          stats.tareas) *
                                                          100,
                                                  )
                                                : 0
                                        }}% del total
                                    </p>
                                    <div
                                        class="h-3 w-full bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden mt-2"
                                    >
                                        <div
                                            class="h-full bg-green-500 rounded-full transition-all duration-700"
                                            :style="{
                                                width: `${stats.tareas > 0 ? Math.min((estadoStats.completado?.tareas / stats.tareas) * 100, 100) : 0}%`,
                                            }"
                                        ></div>
                                    </div>
                                </div>
                                <div
                                    class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-xl transition-all duration-300 p-6 flex flex-col gap-2"
                                >
                                    <p
                                        class="text-sm font-medium text-gray-600 dark:text-gray-300"
                                    >
                                        Integraciones
                                    </p>
                                    <p
                                        class="text-4xl font-extrabold text-purple-600 dark:text-purple-400"
                                    >
                                        {{
                                            estadoStats.completado
                                                ?.integraciones || 0
                                        }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{
                                            stats.integraciones > 0
                                                ? Math.round(
                                                      (estadoStats.completado
                                                          ?.integraciones /
                                                          stats.integraciones) *
                                                          100,
                                                  )
                                                : 0
                                        }}% del total
                                    </p>
                                    <div
                                        class="h-3 w-full bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden mt-2"
                                    >
                                        <div
                                            class="h-full bg-purple-500 rounded-full transition-all duration-700"
                                            :style="{
                                                width: `${stats.integraciones > 0 ? Math.min((estadoStats.completado?.integraciones / stats.integraciones) * 100, 100) : 0}%`,
                                            }"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ESTADO NO APROBADOS -->
                <div
                    v-if="estimacionesPorEstado.no_aprobados?.length > 0"
                    class="mt-12 animate-fade-in-up delay-400"
                >
                    <div
                        class="mb-6 relative before:absolute before:-inset-1 before:rounded-2xl before:bg-gradient-to-r before:from-red-500 before:to-rose-600 before:opacity-0 before:blur-sm hover:before:opacity-10 transition-all duration-300"
                    >
                        <div
                            class="relative bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl p-8 border border-gray-200/50 dark:bg-gray-800/80 dark:border-gray-700/50"
                        >
                            <div
                                class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4"
                            >
                                <div class="flex flex-col gap-2">
                                    <h2
                                        class="text-3xl md:text-4xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-red-600 via-orange-600 to-rose-500 animate-gradient-shift"
                                    >
                                        ❌
                                        {{
                                            getEstadoConfig("no_aprobados")
                                                .title
                                        }}
                                    </h2>
                                    <p
                                        class="text-gray-600 dark:text-gray-300 flex items-center gap-2 text-sm md:text-base"
                                    >
                                        <span
                                            class="h-2 w-2 rounded-full bg-red-500 animate-pulse"
                                        ></span>
                                        <span>{{
                                            getEstadoConfig("no_aprobados")
                                                .description
                                        }}</span>
                                    </p>
                                </div>
                                <div class="hidden md:flex items-center gap-4">
                                    <div
                                        class="flex items-center px-4 py-2 rounded-lg bg-gradient-to-r from-red-500 to-rose-600 text-white font-medium shadow-lg"
                                    >
                                        <span class="font-bold">{{
                                            estadoStats.no_aprobados?.count || 0
                                        }}</span>
                                        <span class="ml-1">{{
                                            getEstadoConfig("no_aprobados")
                                                .labelCount
                                        }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Cards de métricas -->
                            <div
                                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-6"
                            >
                                <div
                                    class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-xl transition-all duration-300 p-6 flex flex-col gap-2"
                                >
                                    <p
                                        class="text-sm font-medium text-gray-600 dark:text-gray-300"
                                    >
                                        Estimaciones
                                    </p>
                                    <p
                                        class="text-4xl font-extrabold text-gray-800 dark:text-white"
                                    >
                                        {{
                                            estadoStats.no_aprobados?.count || 0
                                        }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{
                                            stats.estimaciones > 0
                                                ? Math.round(
                                                      (estadoStats.no_aprobados
                                                          ?.count /
                                                          stats.estimaciones) *
                                                          100,
                                                  )
                                                : 0
                                        }}% del total
                                    </p>
                                    <div
                                        class="h-3 w-full bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden mt-2"
                                    >
                                        <div
                                            class="h-full bg-red-500 rounded-full transition-all duration-700"
                                            :style="{
                                                width: `${stats.estimaciones > 0 ? Math.min((estadoStats.no_aprobados?.count / stats.estimaciones) * 100, 100) : 0}%`,
                                            }"
                                        ></div>
                                    </div>
                                </div>
                                <div
                                    class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-xl transition-all duration-300 p-6 flex flex-col gap-2"
                                >
                                    <p
                                        class="text-sm font-medium text-gray-600 dark:text-gray-300"
                                    >
                                        Horas
                                    </p>
                                    <p
                                        class="text-4xl font-extrabold text-gray-800 dark:text-white"
                                    >
                                        {{
                                            estadoStats.no_aprobados?.horas || 0
                                        }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{
                                            stats.horas > 0
                                                ? Math.round(
                                                      (estadoStats.no_aprobados
                                                          ?.horas /
                                                          stats.horas) *
                                                          100,
                                                  )
                                                : 0
                                        }}% del total
                                    </p>
                                    <div
                                        class="h-3 w-full bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden mt-2"
                                    >
                                        <div
                                            class="h-full bg-orange-500 rounded-full transition-all duration-700"
                                            :style="{
                                                width: `${stats.horas > 0 ? Math.min((estadoStats.no_aprobados?.horas / stats.horas) * 100, 100) : 0}%`,
                                            }"
                                        ></div>
                                    </div>
                                </div>
                                <div
                                    class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-xl transition-all duration-300 p-6 flex flex-col gap-2"
                                >
                                    <p
                                        class="text-sm font-medium text-gray-600 dark:text-gray-300"
                                    >
                                        Tareas
                                    </p>
                                    <p
                                        class="text-4xl font-extrabold text-red-600 dark:text-red-400"
                                    >
                                        {{
                                            estadoStats.no_aprobados?.tareas ||
                                            0
                                        }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{
                                            stats.tareas > 0
                                                ? Math.round(
                                                      (estadoStats.no_aprobados
                                                          ?.tareas /
                                                          stats.tareas) *
                                                          100,
                                                  )
                                                : 0
                                        }}% del total
                                    </p>
                                    <div
                                        class="h-3 w-full bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden mt-2"
                                    >
                                        <div
                                            class="h-full bg-red-500 rounded-full transition-all duration-700"
                                            :style="{
                                                width: `${stats.tareas > 0 ? Math.min((estadoStats.no_aprobados?.tareas / stats.tareas) * 100, 100) : 0}%`,
                                            }"
                                        ></div>
                                    </div>
                                </div>
                                <div
                                    class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-xl transition-all duration-300 p-6 flex flex-col gap-2"
                                >
                                    <p
                                        class="text-sm font-medium text-gray-600 dark:text-gray-300"
                                    >
                                        Integraciones
                                    </p>
                                    <p
                                        class="text-4xl font-extrabold text-purple-600 dark:text-purple-400"
                                    >
                                        {{
                                            estadoStats.no_aprobados
                                                ?.integraciones || 0
                                        }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{
                                            stats.integraciones > 0
                                                ? Math.round(
                                                      (estadoStats.no_aprobados
                                                          ?.integraciones /
                                                          stats.integraciones) *
                                                          100,
                                                  )
                                                : 0
                                        }}% del total
                                    </p>
                                    <div
                                        class="h-3 w-full bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden mt-2"
                                    >
                                        <div
                                            class="h-full bg-purple-500 rounded-full transition-all duration-700"
                                            :style="{
                                                width: `${stats.integraciones > 0 ? Math.min((estadoStats.no_aprobados?.integraciones / stats.integraciones) * 100, 100) : 0}%`,
                                            }"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ESTADO APROBADO -->
                <div
                    v-if="estimacionesPorEstado.aprobado?.length > 0"
                    class="mt-12 animate-fade-in-up delay-500"
                >
                    <div
                        class="mb-6 relative before:absolute before:-inset-1 before:rounded-2xl before:bg-gradient-to-r before:from-emerald-500 before:to-green-600 before:opacity-0 before:blur-sm hover:before:opacity-10 transition-all duration-300"
                    >
                        <div
                            class="relative bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl p-8 border border-gray-200/50 dark:bg-gray-800/80 dark:border-gray-700/50"
                        >
                            <div
                                class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4"
                            >
                                <div class="flex flex-col gap-2">
                                    <h2
                                        class="text-3xl md:text-4xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-emerald-600 via-green-600 to-teal-500 animate-gradient-shift"
                                    >
                                        ✔️
                                        {{ getEstadoConfig("aprobado").title }}
                                    </h2>
                                    <p
                                        class="text-gray-600 dark:text-gray-300 flex items-center gap-2 text-sm md:text-base"
                                    >
                                        <span
                                            class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"
                                        ></span>
                                        <span>{{
                                            getEstadoConfig("aprobado")
                                                .description
                                        }}</span>
                                    </p>
                                </div>
                                <div class="hidden md:flex items-center gap-4">
                                    <div
                                        class="flex items-center px-4 py-2 rounded-lg bg-gradient-to-r from-emerald-500 to-green-600 text-white font-medium shadow-lg"
                                    >
                                        <span class="font-bold">{{
                                            estadoStats.aprobado?.count || 0
                                        }}</span>
                                        <span class="ml-1">{{
                                            getEstadoConfig("aprobado")
                                                .labelCount
                                        }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Cards de métricas -->
                            <div
                                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-6"
                            >
                                <div
                                    class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-xl transition-all duration-300 p-6 flex flex-col gap-2"
                                >
                                    <p
                                        class="text-sm font-medium text-gray-600 dark:text-gray-300"
                                    >
                                        Estimaciones
                                    </p>
                                    <p
                                        class="text-4xl font-extrabold text-gray-800 dark:text-white"
                                    >
                                        {{ estadoStats.aprobado?.count || 0 }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{
                                            stats.estimaciones > 0
                                                ? Math.round(
                                                      (estadoStats.aprobado
                                                          ?.count /
                                                          stats.estimaciones) *
                                                          100,
                                                  )
                                                : 0
                                        }}% del total
                                    </p>
                                    <div
                                        class="h-3 w-full bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden mt-2"
                                    >
                                        <div
                                            class="h-full bg-emerald-500 rounded-full transition-all duration-700"
                                            :style="{
                                                width: `${stats.estimaciones > 0 ? Math.min((estadoStats.aprobado?.count / stats.estimaciones) * 100, 100) : 0}%`,
                                            }"
                                        ></div>
                                    </div>
                                </div>
                                <div
                                    class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-xl transition-all duration-300 p-6 flex flex-col gap-2"
                                >
                                    <p
                                        class="text-sm font-medium text-gray-600 dark:text-gray-300"
                                    >
                                        Horas
                                    </p>
                                    <p
                                        class="text-4xl font-extrabold text-gray-800 dark:text-white"
                                    >
                                        {{ estadoStats.aprobado?.horas || 0 }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{
                                            stats.horas > 0
                                                ? Math.round(
                                                      (estadoStats.aprobado
                                                          ?.horas /
                                                          stats.horas) *
                                                          100,
                                                  )
                                                : 0
                                        }}% del total
                                    </p>
                                    <div
                                        class="h-3 w-full bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden mt-2"
                                    >
                                        <div
                                            class="h-full bg-green-500 rounded-full transition-all duration-700"
                                            :style="{
                                                width: `${stats.horas > 0 ? Math.min((estadoStats.aprobado?.horas / stats.horas) * 100, 100) : 0}%`,
                                            }"
                                        ></div>
                                    </div>
                                </div>
                                <div
                                    class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-xl transition-all duration-300 p-6 flex flex-col gap-2"
                                >
                                    <p
                                        class="text-sm font-medium text-gray-600 dark:text-gray-300"
                                    >
                                        Tareas
                                    </p>
                                    <p
                                        class="text-4xl font-extrabold text-blue-600 dark:text-blue-400"
                                    >
                                        {{ estadoStats.aprobado?.tareas || 0 }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{
                                            stats.tareas > 0
                                                ? Math.round(
                                                      (estadoStats.aprobado
                                                          ?.tareas /
                                                          stats.tareas) *
                                                          100,
                                                  )
                                                : 0
                                        }}% del total
                                    </p>
                                    <div
                                        class="h-3 w-full bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden mt-2"
                                    >
                                        <div
                                            class="h-full bg-blue-500 rounded-full transition-all duration-700"
                                            :style="{
                                                width: `${stats.tareas > 0 ? Math.min((estadoStats.aprobado?.tareas / stats.tareas) * 100, 100) : 0}%`,
                                            }"
                                        ></div>
                                    </div>
                                </div>
                                <div
                                    class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-xl transition-all duration-300 p-6 flex flex-col gap-2"
                                >
                                    <p
                                        class="text-sm font-medium text-gray-600 dark:text-gray-300"
                                    >
                                        Integraciones
                                    </p>
                                    <p
                                        class="text-4xl font-extrabold text-purple-600 dark:text-purple-400"
                                    >
                                        {{
                                            estadoStats.aprobado
                                                ?.integraciones || 0
                                        }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{
                                            stats.integraciones > 0
                                                ? Math.round(
                                                      (estadoStats.aprobado
                                                          ?.integraciones /
                                                          stats.integraciones) *
                                                          100,
                                                  )
                                                : 0
                                        }}% del total
                                    </p>
                                    <div
                                        class="h-3 w-full bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden mt-2"
                                    >
                                        <div
                                            class="h-full bg-purple-500 rounded-full transition-all duration-700"
                                            :style="{
                                                width: `${stats.integraciones > 0 ? Math.min((estadoStats.aprobado?.integraciones / stats.integraciones) * 100, 100) : 0}%`,
                                            }"
                                        ></div>
                                    </div>
                                </div>
                            </div>
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
                                        {{ stats.estimaciones }}
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

                        <!-- Tasas por Estado - Versión estática sin errores -->
                        <div
                            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3"
                        >
                            <!-- Pendiente -->
                            <div
                                v-if="estadoStats.pendiente?.count > 0"
                                class="group relative bg-white/5 backdrop-blur-sm rounded-xl p-4 border border-white/10 hover:border-white/30 hover:bg-white/10 transition-all duration-300 overflow-hidden"
                            >
                                <div
                                    class="absolute inset-0 opacity-5 group-hover:opacity-10 transition-opacity duration-300 bg-gradient-to-r from-blue-500 to-cyan-600"
                                ></div>
                                <div
                                    class="relative flex items-start space-x-3"
                                >
                                    <div class="flex-shrink-0 mt-1">
                                        <div
                                            class="w-3 h-3 rounded-full bg-blue-600 dark:bg-blue-400"
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
                                                        estadoStats.pendiente
                                                            .count
                                                    }}
                                                </p>
                                                <p
                                                    class="text-white/80 text-xs mt-0.5"
                                                >
                                                    pendientes
                                                </p>
                                            </div>
                                            <div
                                                class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300"
                                            >
                                                {{
                                                    stats.estimaciones > 0
                                                        ? Math.round(
                                                              (estadoStats
                                                                  .pendiente
                                                                  .count /
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
                                                                  (estadoStats
                                                                      .pendiente
                                                                      .count /
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
                                                    class="h-full rounded-full transition-all duration-700 bg-gradient-to-r from-blue-500 to-cyan-600"
                                                    :style="{
                                                        width: `${stats.estimaciones > 0 ? Math.min((estadoStats.pendiente.count / stats.estimaciones) * 100, 100) : 0}%`,
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
                                                        estadoStats.pendiente
                                                            .horas
                                                    }}</span
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="absolute -top-2 -right-2 h-8 w-8 rounded-full opacity-10 bg-blue-600"
                                ></div>
                            </div>

                            <!-- En Proceso -->
                            <div
                                v-if="estadoStats.en_proceso?.count > 0"
                                class="group relative bg-white/5 backdrop-blur-sm rounded-xl p-4 border border-white/10 hover:border-white/30 hover:bg-white/10 transition-all duration-300 overflow-hidden"
                            >
                                <div
                                    class="absolute inset-0 opacity-5 group-hover:opacity-10 transition-opacity duration-300 bg-gradient-to-r from-amber-500 to-orange-600"
                                ></div>
                                <div
                                    class="relative flex items-start space-x-3"
                                >
                                    <div class="flex-shrink-0 mt-1">
                                        <div
                                            class="w-3 h-3 rounded-full bg-amber-600 dark:bg-amber-400"
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
                                                        estadoStats.en_proceso
                                                            .count
                                                    }}
                                                </p>
                                                <p
                                                    class="text-white/80 text-xs mt-0.5"
                                                >
                                                    en proceso
                                                </p>
                                            </div>
                                            <div
                                                class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300"
                                            >
                                                {{
                                                    stats.estimaciones > 0
                                                        ? Math.round(
                                                              (estadoStats
                                                                  .en_proceso
                                                                  .count /
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
                                                                  (estadoStats
                                                                      .en_proceso
                                                                      .count /
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
                                                    class="h-full rounded-full transition-all duration-700 bg-gradient-to-r from-amber-500 to-orange-600"
                                                    :style="{
                                                        width: `${stats.estimaciones > 0 ? Math.min((estadoStats.en_proceso.count / stats.estimaciones) * 100, 100) : 0}%`,
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
                                                        estadoStats.en_proceso
                                                            .horas
                                                    }}</span
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="absolute -top-2 -right-2 h-8 w-8 rounded-full opacity-10 bg-amber-600"
                                ></div>
                            </div>

                            <!-- Completado -->
                            <div
                                v-if="estadoStats.completado?.count > 0"
                                class="group relative bg-white/5 backdrop-blur-sm rounded-xl p-4 border border-white/10 hover:border-white/30 hover:bg-white/10 transition-all duration-300 overflow-hidden"
                            >
                                <div
                                    class="absolute inset-0 opacity-5 group-hover:opacity-10 transition-opacity duration-300 bg-gradient-to-r from-green-500 to-emerald-600"
                                ></div>
                                <div
                                    class="relative flex items-start space-x-3"
                                >
                                    <div class="flex-shrink-0 mt-1">
                                        <div
                                            class="w-3 h-3 rounded-full bg-green-600 dark:bg-green-400"
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
                                                        estadoStats.completado
                                                            .count
                                                    }}
                                                </p>
                                                <p
                                                    class="text-white/80 text-xs mt-0.5"
                                                >
                                                    completadas
                                                </p>
                                            </div>
                                            <div
                                                class="px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300"
                                            >
                                                {{
                                                    stats.estimaciones > 0
                                                        ? Math.round(
                                                              (estadoStats
                                                                  .completado
                                                                  .count /
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
                                                                  (estadoStats
                                                                      .completado
                                                                      .count /
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
                                                    class="h-full rounded-full transition-all duration-700 bg-gradient-to-r from-green-500 to-emerald-600"
                                                    :style="{
                                                        width: `${stats.estimaciones > 0 ? Math.min((estadoStats.completado.count / stats.estimaciones) * 100, 100) : 0}%`,
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
                                                        estadoStats.completado
                                                            .horas
                                                    }}</span
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="absolute -top-2 -right-2 h-8 w-8 rounded-full opacity-10 bg-green-600"
                                ></div>
                            </div>

                            <!-- No Aprobados -->
                            <div
                                v-if="estadoStats.no_aprobados?.count > 0"
                                class="group relative bg-white/5 backdrop-blur-sm rounded-xl p-4 border border-white/10 hover:border-white/30 hover:bg-white/10 transition-all duration-300 overflow-hidden"
                            >
                                <div
                                    class="absolute inset-0 opacity-5 group-hover:opacity-10 transition-opacity duration-300 bg-gradient-to-r from-red-500 to-rose-600"
                                ></div>
                                <div
                                    class="relative flex items-start space-x-3"
                                >
                                    <div class="flex-shrink-0 mt-1">
                                        <div
                                            class="w-3 h-3 rounded-full bg-red-600 dark:bg-red-400"
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
                                                        estadoStats.no_aprobados
                                                            .count
                                                    }}
                                                </p>
                                                <p
                                                    class="text-white/80 text-xs mt-0.5"
                                                >
                                                    no aprobadas
                                                </p>
                                            </div>
                                            <div
                                                class="px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300"
                                            >
                                                {{
                                                    stats.estimaciones > 0
                                                        ? Math.round(
                                                              (estadoStats
                                                                  .no_aprobados
                                                                  .count /
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
                                                                  (estadoStats
                                                                      .no_aprobados
                                                                      .count /
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
                                                    class="h-full rounded-full transition-all duration-700 bg-gradient-to-r from-red-500 to-rose-600"
                                                    :style="{
                                                        width: `${stats.estimaciones > 0 ? Math.min((estadoStats.no_aprobados.count / stats.estimaciones) * 100, 100) : 0}%`,
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
                                                        estadoStats.no_aprobados
                                                            .horas
                                                    }}</span
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="absolute -top-2 -right-2 h-8 w-8 rounded-full opacity-10 bg-red-600"
                                ></div>
                            </div>

                            <!-- Aprobado -->
                            <div
                                v-if="estadoStats.aprobado?.count > 0"
                                class="group relative bg-white/5 backdrop-blur-sm rounded-xl p-4 border border-white/10 hover:border-white/30 hover:bg-white/10 transition-all duration-300 overflow-hidden"
                            >
                                <div
                                    class="absolute inset-0 opacity-5 group-hover:opacity-10 transition-opacity duration-300 bg-gradient-to-r from-emerald-500 to-green-600"
                                ></div>
                                <div
                                    class="relative flex items-start space-x-3"
                                >
                                    <div class="flex-shrink-0 mt-1">
                                        <div
                                            class="w-3 h-3 rounded-full bg-emerald-600 dark:bg-emerald-400"
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
                                                        estadoStats.aprobado
                                                            .count
                                                    }}
                                                </p>
                                                <p
                                                    class="text-white/80 text-xs mt-0.5"
                                                >
                                                    aprobadas
                                                </p>
                                            </div>
                                            <div
                                                class="px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300"
                                            >
                                                {{
                                                    stats.estimaciones > 0
                                                        ? Math.round(
                                                              (estadoStats
                                                                  .aprobado
                                                                  .count /
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
                                                                  (estadoStats
                                                                      .aprobado
                                                                      .count /
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
                                                    class="h-full rounded-full transition-all duration-700 bg-gradient-to-r from-emerald-500 to-green-600"
                                                    :style="{
                                                        width: `${stats.estimaciones > 0 ? Math.min((estadoStats.aprobado.count / stats.estimaciones) * 100, 100) : 0}%`,
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
                                                        estadoStats.aprobado
                                                            .horas
                                                    }}</span
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="absolute -top-2 -right-2 h-8 w-8 rounded-full opacity-10 bg-emerald-600"
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

                <!-- ==================== SUB-SECCIÓN 2.9: ACCESO RÁPIDO ==================== -->
                <div class="mt-8 animate-fade-in-up delay-700">
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
                        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 animate-fade-in-up delay-800"
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
.animate-fade-in-up.delay-800 {
    animation-delay: 0.8s;
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
