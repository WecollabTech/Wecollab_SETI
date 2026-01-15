<script setup>
import { Head, router } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import PageHeader from "@/Components/Layout/PageHeader.vue";
import TableCard from "@/Components/Table/TableCard.vue";

defineProps({
    integracion: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Head :title="`Integración - ${integracion.nombre}`" />

    <AppLayout>
        <template #title>
            <PageHeader title="Detalle de Integración" />
        </template>

        <div class="max-w-6xl mx-auto px-4 space-y-6">
            <!-- RESUMEN -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <div
                    class="flex flex-col md:flex-row md:items-start md:justify-between gap-4"
                >
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800">
                            {{ integracion.nombre }}
                        </h2>
                        <p class="text-sm text-gray-600 mt-1">
                            {{ integracion.descripcion || "Sin descripción" }}
                        </p>
                    </div>

                    <!-- ACCIONES -->
                    <div class="flex gap-2">
                        <button
                            @click="router.get('/integraciones')"
                            class="px-3 py-2 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200"
                        >
                            ← Volver
                        </button>

                        <button
                            @click="
                                router.get(
                                    route('integraciones.edit', integracion.id)
                                )
                            "
                            class="px-3 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700"
                        >
                            Editar
                        </button>
                    </div>
                </div>
            </div>

            <!-- TABLA: TIPOS DE IMPLEMENTACIÓN -->
            <TableCard
                title="Tipos de Implementación"
                :headers="['Nombre', 'Estado']"
                :rows="
                    integracion.tipos_implementacion?.map((t) => [
                        t.nombre,
                        t.estado ? 'Activo' : 'Inactivo',
                    ]) || []
                "
            />

            <!-- FUTURO -->
            <TableCard
                title="Tareas asociadas"
                :headers="['Nombre', 'Responsable', 'Estado']"
                :rows="[]"
            />
        </div>
    </AppLayout>
</template>
