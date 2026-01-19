<script setup>
import { Head, router } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import PageHeader from "@/Components/Layout/PageHeader.vue";
import TableCard from "@/Components/Table/TableCard.vue";
import { ref } from "vue";

// Props: tipo de implementación
const props = defineProps({
    tipo: {
        type: Object,
        default: () => ({}),
    },
});

// Funciones de navegación
const goBack = () => router.get("/tipoimplementacion");
const goEdit = () =>
    router.get(route("tipoimplementacion.edit", props.tipo.id));
</script>

<template>
    <Head title="Detalle Tipo de Implementación" />

    <AppLayout>
        <template #title>
            <PageHeader title="Detalle de Tipo de Implementación" />
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
                        {{ props.tipo.nombre || "Sin nombre" }}
                    </h2>

                    <div class="flex items-center gap-2">
                        <span
                            :class="[
                                props.tipo.estado
                                    ? 'bg-green-100 text-green-800 px-3 py-1 rounded-full font-semibold'
                                    : 'bg-red-100 text-red-800 px-3 py-1 rounded-full font-semibold',
                            ]"
                        >
                            {{ props.tipo.estado ? "Activo" : "Inactivo" }}
                        </span>
                        <div
                            :class="
                                props.tipo.estado
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
                            Rúbrica
                        </h3>
                        <p class="text-gray-700">
                            {{ props.tipo.rubrica || "Sin rúbrica" }}
                        </p>

                        <h3
                            class="font-semibold text-gray-600 text-sm uppercase mt-4 mb-2"
                        >
                            Descripción
                        </h3>
                        <p class="text-gray-700">
                            {{ props.tipo.descripcion || "Sin descripción" }}
                        </p>
                    </div>

                    <!-- Integraciones y Fases -->
                    <div
                        class="bg-gray-50 p-5 rounded-xl shadow-inner hover:shadow transition"
                    >
                        <h3
                            class="font-semibold text-gray-600 text-sm uppercase mb-2"
                        >
                            Integraciones
                        </h3>
                        <ul class="list-disc list-inside text-gray-700 mb-4">
                            <li
                                v-for="int in props.tipo.integraciones || []"
                                :key="int.id"
                            >
                                {{ int.nombre }}
                            </li>
                            <li
                                v-if="
                                    !(
                                        props.tipo.integraciones &&
                                        props.tipo.integraciones.length
                                    )
                                "
                            >
                                Sin integraciones
                            </li>
                        </ul>

                        <h3
                            class="font-semibold text-gray-600 text-sm uppercase mb-2"
                        >
                            Fases
                        </h3>
                        <ul class="list-disc list-inside text-gray-700">
                            <li
                                v-for="fase in props.tipo.fases || []"
                                :key="fase.id"
                            >
                                {{ fase.nombre }}
                            </li>
                            <li
                                v-if="
                                    !(
                                        props.tipo.fases &&
                                        props.tipo.fases.length
                                    )
                                "
                            >
                                Sin fases
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- FOOTER BOTONES -->
                <!-- FOOTER BOTONES -->
                <div
                    class="px-6 py-4 flex justify-center gap-3 bg-gray-50 border-t border-gray-100"
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
                    ['Nombre', props.tipo.nombre || '-'],
                    ['Rúbrica', props.tipo.rubrica || '-'],
                    ['Descripción', props.tipo.descripcion || '-'],
                    ['Estado', props.tipo.estado ? 'Activo' : 'Inactivo'],
                    [
                        'Integraciones',
                        props.tipo.integraciones
                            ?.map((i) => i.nombre)
                            .join(', ') || '-',
                    ],
                    [
                        'Fases',
                        props.tipo.fases?.map((f) => f.nombre).join(', ') ||
                            '-',
                    ],
                ]"
            />
        </div>
    </AppLayout>
</template>
