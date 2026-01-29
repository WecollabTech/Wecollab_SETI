<script setup>
import { Head, router, usePage } from "@inertiajs/vue3";
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import TablaSeccion from "@/Components/Layout/TablaSeccion.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import PageHeader from "@/Components/Layout/PageHeader.vue";

/**
 * ===============================
 * PROPS DESDE INERTIA
 * ===============================
 */
const page = usePage();
const estimacionId = computed(() => page.props.estimacionId);

/**
 * ===============================
 * STATE
 * ===============================
 */
const estimacion = ref(null);
const isLoading = ref(false);
const hasError = ref(false);

/**
 * ===============================
 * API
 * ===============================
 */
const fetchEstimacion = async () => {
    if (!estimacionId.value) return;

    isLoading.value = true;
    hasError.value = false;

    try {
        const { data } = await axios.get(
            `/api/estimaciones/${estimacionId.value}`,
        );
        estimacion.value = data ?? null;
    } catch (error) {
        hasError.value = true;
        console.error("Error al cargar la estimación:", error);
    } finally {
        isLoading.value = false;
    }
};

onMounted(fetchEstimacion);

/**
 * ===============================
 * NORMALIZACIÓN DE FASES
 * ===============================
 */
const fasesNormalizadas = computed(() => {
    return (
        estimacion.value?.fases?.map((fase) => {
            const tareas = Array.isArray(fase.tareas) ? fase.tareas : [];

            const totalMinutos = tareas.reduce(
                (total, tarea) => total + Number(tarea.duracion_minuto || 0),
                0,
            );

            return {
                ...fase,
                tareas,
                total_minutos: totalMinutos,
                total_horas: (totalMinutos / 60).toFixed(1),
            };
        }) ?? []
    );
});

/**
 * ===============================
 * NORMALIZACIÓN DE INTEGRACIONES
 * ===============================
 */
const integracionesNormalizadas = computed(() => {
    return (
        estimacion.value?.integraciones?.map((integracion) => {
            const tareas = Array.isArray(integracion.tareas)
                ? integracion.tareas
                : [];

            const totalMinutos = tareas.reduce(
                (total, tarea) => total + Number(tarea.duracion_minuto || 0),
                0,
            );

            return {
                ...integracion,
                tareas,
                total_minutos: totalMinutos,
                total_horas: (totalMinutos / 60).toFixed(1),
            };
        }) ?? []
    );
});

const tieneFases = computed(() => fasesNormalizadas.value.length > 0);
const tieneIntegraciones = computed(
    () => integracionesNormalizadas.value.length > 0,
);
</script>

<template>
    <Head title="Detalle de Estimación" />

    <AppLayout class="bg-gray-50">
        <template #title>
            <PageHeader title="Detalle de Estimación" />
        </template>

        <!-- ================= LOADING ================= -->
        <div
            v-if="isLoading"
            class="flex justify-center items-center py-16 text-gray-500"
        >
            Cargando estimación...
        </div>

        <!-- ================= ERROR ================= -->
        <div
            v-else-if="hasError"
            class="text-center py-16 text-red-500 font-medium"
        >
            Ocurrió un error al cargar la estimación.
        </div>

        <!-- ================= CONTENIDO ================= -->
        <div v-else-if="estimacion">
            <!-- CONTENEDOR CENTRAL -->
            <div class="max-w-7xl mx-auto px-4 md:px-6 py-10 space-y-12">
                <!-- ================= INFO GENERAL ================= -->
                <section
                    class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 md:p-8 grid grid-cols-1 md:grid-cols-3 gap-6"
                >
                    <div>
                        <p
                            class="text-xs uppercase tracking-wide text-gray-400"
                        >
                            Tipo de Implementación
                        </p>
                        <p class="font-semibold text-gray-800">
                            {{ estimacion.nombre_tipo_implementacion }}
                        </p>
                    </div>

                    <div>
                        <p
                            class="text-xs uppercase tracking-wide text-gray-400"
                        >
                            Complejidad
                        </p>
                        <p class="font-semibold text-gray-800">
                            {{ estimacion.complejidad?.nombre ?? "-" }}
                        </p>
                    </div>

                    <div>
                        <p
                            class="text-xs uppercase tracking-wide text-gray-400"
                        >
                            Total Horas
                        </p>
                        <p class="text-2xl font-bold text-blue-600">
                            {{ estimacion.total_horas }} h
                        </p>
                    </div>

                    <div class="md:col-span-3">
                        <p
                            class="text-xs uppercase tracking-wide text-gray-400"
                        >
                            Comentarios
                        </p>
                        <p class="text-gray-700 leading-relaxed mt-1">
                            {{ estimacion.comentarios || "Sin comentarios" }}
                        </p>
                    </div>
                </section>

                <!-- ================= FASES ================= -->
                <section v-if="tieneFases" class="space-y-6">
                    <h2 class="text-xl font-semibold text-gray-800">
                        Fases del Proyecto
                    </h2>

                    <div
                        v-for="fase in fasesNormalizadas"
                        :key="fase.id"
                        class="bg-white rounded-3xl shadow-sm border border-blue-100 overflow-hidden"
                    >
                        <!-- HEADER -->
                        <div
                            class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 px-6 py-4 bg-blue-50"
                        >
                            <div>
                                <p class="text-blue-800 font-semibold">
                                    {{ fase.nombre }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    {{ fase.objetivo }}
                                </p>
                            </div>

                            <span
                                class="inline-flex items-center gap-2 bg-blue-100 text-blue-800 px-4 py-1.5 rounded-full text-sm font-semibold"
                            >
                                ⏱ {{ fase.total_horas }} h
                            </span>
                        </div>

                        <!-- TAREAS -->
                        <div class="px-6 py-4 divide-y">
                            <div
                                v-for="tarea in fase.tareas"
                                :key="tarea.id"
                                class="flex justify-between items-center py-2"
                            >
                                <div class="flex items-start gap-2">
                                    <span
                                        class="mt-2 w-2 h-2 rounded-full bg-blue-400"
                                    ></span>
                                    <p class="text-gray-700">
                                        {{ tarea.nombre_tarea }}
                                    </p>
                                </div>

                                <span class="text-sm font-medium text-gray-600">
                                    {{
                                        (tarea.duracion_minuto / 60).toFixed(1)
                                    }}
                                    h
                                </span>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ================= INTEGRACIONES ================= -->
                <section v-if="tieneIntegraciones" class="space-y-6">
                    <h2 class="text-xl font-semibold text-gray-800">
                        Integraciones
                    </h2>

                    <div
                        v-for="integracion in integracionesNormalizadas"
                        :key="integracion.id"
                        class="bg-white rounded-3xl shadow-sm border border-purple-100 overflow-hidden"
                    >
                        <!-- HEADER -->
                        <div
                            class="flex items-center justify-between px-6 py-4 bg-purple-50"
                        >
                            <p class="text-purple-800 font-semibold">
                                {{ integracion.nombre }}
                            </p>

                            <span
                                class="inline-flex items-center gap-2 bg-purple-100 text-purple-800 px-4 py-1.5 rounded-full text-sm font-semibold"
                            >
                                ⏱ {{ integracion.total_horas }} h
                            </span>
                        </div>

                        <!-- TAREAS -->
                        <div class="px-6 py-4 divide-y">
                            <div
                                v-for="tarea in integracion.tareas"
                                :key="tarea.id"
                                class="flex justify-between items-center py-2"
                            >
                                <div class="flex items-start gap-2">
                                    <span
                                        class="mt-2 w-2 h-2 rounded-full bg-purple-400"
                                    ></span>
                                    <p class="text-gray-700">
                                        {{ tarea.nombre_tarea }}
                                    </p>
                                </div>

                                <span class="text-sm font-medium text-gray-600">
                                    {{
                                        (tarea.duracion_minuto / 60).toFixed(1)
                                    }}
                                    h
                                </span>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ================= ACCIONES ================= -->
                <div class="flex justify-end">
                    <button
                        @click="router.get(route('estimaciones'))"
                        class="px-8 py-2.5 rounded-xl bg-gray-700 text-white font-semibold hover:bg-gray-800 transition shadow"
                    >
                        ← Volver a estimaciones
                    </button>
                </div>
            </div>
        </div>

        <!-- ================= SIN DATA ================= -->
        <div v-else class="text-center py-16 text-gray-500">
            No se encontró la estimación.
        </div>
    </AppLayout>
</template>
