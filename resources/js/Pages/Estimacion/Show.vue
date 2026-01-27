<script setup>
import { Head, router, usePage } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import PageHeader from "@/Components/Layout/PageHeader.vue";
import TablaSeccion from "@/Components/Layout/TablaSeccion.vue";

import { ref, onMounted } from "vue";
import axios from "axios";

// ===============================
// PROPS DESDE WEB.PHP
// ===============================
const { props } = usePage();
const estimacionId = props.estimacionId;

// ===============================
// STATE
// ===============================
const estimacion = ref(null);
const loading = ref(true);

// ===============================
// API
// ===============================
const cargarEstimacion = async () => {
    loading.value = true;
    try {
        const res = await axios.get(`/api/estimaciones/${estimacionId}`);
        estimacion.value = res.data;
    } catch (error) {
        console.error("Error al cargar la estimación:", error);
    } finally {
        loading.value = false;
    }
};

// ===============================
// MOUNT
// ===============================
onMounted(() => {
    cargarEstimacion();
});
</script>

<template>
    <Head title="Detalle de Estimación" />

    <AppLayout>
        <template #title>
            <PageHeader title="Detalle de Estimación" />
        </template>

        <!-- LOADING -->
        <div v-if="loading" class="text-center py-6 text-gray-500">
            Cargando estimación...
        </div>

        <!-- CONTENIDO -->
        <div v-else-if="estimacion" class="space-y-6">
            <!-- ================= INFO GENERAL ================= -->
            <div
                class="bg-white rounded-lg shadow p-6 grid grid-cols-1 md:grid-cols-3 gap-4"
            >
                <div>
                    <p class="text-sm text-gray-500">Tipo de Implementación</p>
                    <p class="font-semibold">
                        {{ estimacion.nombre_tipo_implementacion }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Complejidad</p>
                    <p class="font-semibold">
                        {{ estimacion.complejidad?.nombre ?? "-" }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Horas</p>
                    <p class="font-semibold text-blue-600">
                        {{ estimacion.total_horas }} hrs
                    </p>
                </div>

                <div class="md:col-span-3">
                    <p class="text-sm text-gray-500">Comentarios</p>
                    <p class="text-gray-700">
                        {{ estimacion.comentarios || "Sin comentarios" }}
                    </p>
                </div>
            </div>

            <!-- ================= FASES ================= -->
            <TablaSeccion
                v-if="estimacion.fases?.length"
                :data="estimacion.fases"
                title="Fases de la Estimación"
            >
                <template #head>
                    <tr class="bg-blue-100 text-blue-900 text-sm uppercase">
                        <th class="py-3 px-3 text-left">Fase</th>
                        <th class="py-3 px-3 text-left">Tarea</th>
                        <th class="py-3 px-3 text-left">Duración (min)</th>
                    </tr>
                </template>

                <template #body>
                    <template v-for="fase in estimacion.fases" :key="fase.id">
                        <tr
                            v-for="tarea in fase.tareas"
                            :key="tarea.id"
                            class="border-b hover:bg-blue-50"
                        >
                            <td class="py-3 px-3 font-medium">
                                {{ fase.nombre }}
                            </td>
                            <td class="py-3 px-3">
                                {{ tarea.nombre_tarea }}
                            </td>
                            <td class="py-3 px-3">
                                {{ tarea.duracion_minuto }} min
                            </td>
                        </tr>
                    </template>
                </template>
            </TablaSeccion>

            <!-- ================= INTEGRACIONES ================= -->
            <TablaSeccion
                v-if="estimacion.integraciones?.length"
                :data="estimacion.integraciones"
                title="Integraciones"
            >
                <template #head>
                    <tr class="bg-purple-100 text-purple-900 text-sm uppercase">
                        <th class="py-3 px-3 text-left">Integración</th>
                        <th class="py-3 px-3 text-left">Tarea</th>
                        <th class="py-3 px-3 text-left">Duración (min)</th>
                    </tr>
                </template>

                <template #body>
                    <template
                        v-for="integracion in estimacion.integraciones"
                        :key="integracion.id"
                    >
                        <tr
                            v-for="tarea in integracion.tareas"
                            :key="tarea.id"
                            class="border-b hover:bg-purple-50"
                        >
                            <td class="py-3 px-3 font-medium">
                                {{ integracion.nombre }}
                            </td>
                            <td class="py-3 px-3">
                                {{ tarea.nombre_tarea }}
                            </td>
                            <td class="py-3 px-3">
                                {{ tarea.duracion_minuto }} min
                            </td>
                        </tr>
                    </template>
                </template>
            </TablaSeccion>

            <!-- ================= ACCIONES ================= -->
            <div class="flex justify-end gap-3">
                <button
                    @click="router.get(route('estimaciones'))"
                    class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700"
                >
                    Volver
                </button>
            </div>
        </div>

        <!-- SIN DATA -->
        <div v-else class="text-center py-6 text-gray-500">
            No se encontró la estimación.
        </div>
    </AppLayout>
</template>
