<script setup>
import { Head, router } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import PageHeader from "@/Components/Layout/PageHeader.vue";
import TableCard from "@/Components/Table/TableCard.vue";

// Props: tarea
const props = defineProps({
    tarea: {
        type: Object,
        default: () => ({}),
    },
});

// Funciones de navegación
const goBack = () => router.get("/tareas");
const goEdit = () => router.get(route("tareas.edit", props.tarea.id));
</script>

<template>
    <Head title="Detalle Tarea" />

    <AppLayout>
        <template #title>
            <PageHeader title="Detalle de Tarea" />
        </template>

        <div class="container my-8 max-w-6xl mx-auto space-y-6">
            <!-- CARD PRINCIPAL -->
            <div
                class="bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden transform transition hover:scale-[1.01]"
            >
                <!-- HEADER -->
                <div
                    class="bg-gradient-to-r from-blue-700 to-blue-900 text-white px-6 py-4 flex flex-col md:flex-row md:justify-between md:items-center gap-4"
                >
                    <h2
                        class="text-2xl md:text-3xl font-bold flex items-center gap-2"
                    >
                        <svg
                            class="w-7 h-7"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 8c-3.866 0-7 3.134-7 7h14c0-3.866-3.134-7-7-7z"
                            />
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 12v6"
                            />
                        </svg>
                        {{ props.tarea.titulo || "Tarea sin título" }}
                    </h2>

                    <div class="flex items-center gap-2">
                        <span
                            :class="[
                                props.tarea.activo
                                    ? 'bg-green-100 text-green-800 px-3 py-1 rounded-full font-semibold'
                                    : 'bg-red-100 text-red-800 px-3 py-1 rounded-full font-semibold',
                            ]"
                        >
                            {{ props.tarea.activo ? "Activo" : "Inactivo" }}
                        </span>
                        <div
                            :class="
                                props.tarea.activo
                                    ? 'bg-green-500'
                                    : 'bg-red-500'
                            "
                            class="w-4 h-4 rounded-full animate-ping"
                        ></div>
                    </div>
                </div>

                <!-- BODY -->
                <div
                    class="px-6 py-6 md:py-8 grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8"
                >
                    <!-- Información principal -->
                    <div
                        class="bg-gray-50 p-5 rounded-xl shadow-inner hover:shadow transition"
                    >
                        <h3
                            class="font-semibold text-gray-600 text-sm uppercase mb-2"
                        >
                            Descripción
                        </h3>
                        <p class="text-gray-700">
                            {{ props.tarea.descripcion || "Sin descripción" }}
                        </p>
                        <p class="mt-3 text-gray-700 font-semibold">
                            Duración:
                            {{ props.tarea.duracion_minuto ?? "-" }} minutos
                        </p>
                        <p class="mt-1 text-gray-700 font-semibold">
                            Orden: {{ props.tarea.orden ?? "-" }}
                        </p>
                        <p class="mt-1 text-gray-700 font-semibold">
                            Fase: {{ props.tarea.fase?.nombre ?? "-" }}
                        </p>
                    </div>

                    <!-- Estado visual -->
                    <div
                        class="bg-gray-50 p-5 rounded-xl shadow-inner hover:shadow transition flex flex-col justify-center items-center"
                    >
                        <h3
                            class="font-semibold text-gray-600 text-sm uppercase mb-2"
                        >
                            Estado Visual
                        </h3>
                        <div
                            :class="[
                                props.tarea.activo
                                    ? 'bg-green-500'
                                    : 'bg-red-500',
                            ]"
                            class="w-16 h-16 rounded-full flex items-center justify-center text-white text-xl font-bold animate-pulse"
                        >
                            {{ props.tarea.activo ? "✔" : "✖" }}
                        </div>
                    </div>
                </div>

                <!-- FOOTER BOTONES -->
                <div
                    class="px-6 py-4 flex justify-end gap-3 bg-gray-50 border-t border-gray-100"
                >
                    <button
                        @click="goBack"
                        class="px-5 py-2 rounded-lg bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium transition"
                    >
                        ← Volver
                    </button>
                    <button
                        @click="goEdit"
                        class="px-5 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-medium transition"
                    >
                        Editar
                    </button>
                </div>
            </div>

            <!-- TABLA DETALLES -->
            <TableCard
                title="Detalles rápidos"
                :headers="['Campo', 'Valor']"
                :rows="[
                    ['Título', props.tarea.titulo || '-'],
                    ['Fase', props.tarea.fase?.nombre || '-'],
                    [
                        'Duración',
                        props.tarea.duracion_minuto
                            ? props.tarea.duracion_minuto + ' min'
                            : '-',
                    ],
                    ['Orden', props.tarea.orden || '-'],
                    ['Estado', props.tarea.activo ? 'Activo' : 'Inactivo'],
                ]"
            />
        </div>
    </AppLayout>
</template>

<style scoped>
.animate-spin {
    animation: spin 1s linear infinite;
}
@keyframes spin {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}
</style>
