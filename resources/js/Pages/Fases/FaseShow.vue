<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import ShowTable from "@/Components/Show/ShowTable.vue";
import { Head, usePage } from "@inertiajs/vue3";

const { fase } = usePage().props;
</script>

<template>
    <Head title="Detalle de Fase" />

    <AppLayout>
        <div class="max-w-6xl mx-auto p-6">
            <!-- INFO GENERAL -->
            <ShowTable
                title="Información de la Fase"
                :headers="['Campo', 'Valor']"
                :rows="[
                    ['Nombre', fase.nombre],
                    ['Descripción', fase.descripcion ?? '—'],
                    ['Minutos Base', fase.minutos_base],
                    ['Estado', fase.activo ? 'Activo' : 'Inactivo'],
                ]"
            />

            <!-- TAREAS -->
            <ShowTable
                title="Tareas de la Fase"
                :headers="['Nombre', 'Estado']"
                :rows="
                    fase.tareas?.map((t) => [
                        t.nombre,
                        t.activo ? 'Activo' : 'Inactivo',
                    ]) ?? []
                "
            />
        </div>
    </AppLayout>
</template>
