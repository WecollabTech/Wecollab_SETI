<script setup>
import { Head } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import Tabla from "./Tabla.vue";
import PageHeader from "@/Components/Layout/PageHeader.vue";
</script>

<template>
    <Head title="Tipo de Implementación" />

    <AppLayout>
        <!-- Slot de título usando PageHeader -->
        <template #title>
            <PageHeader title="Proyectos de Implementación" />
        </template>

        <!-- Contenido principal -->
        <div class="max-w-[1100px] mx-auto mt-4">
            <Tabla />
        </div>
    </AppLayout>
</template>
