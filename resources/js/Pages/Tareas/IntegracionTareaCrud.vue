<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import TaskForm from "@/Components/Tareas/TaskForm.vue";
import { usePage } from "@inertiajs/vue3";

const page = usePage();

const integracionFromUrl =
    page.props?.query?.integracion_id ||
    new URLSearchParams(window.location.search).get("integracion_id");
</script>

<template>
    <AppLayout>
        <TaskForm
            type="integracion"
            :integracion-id="integracionFromUrl"
            store-url="/api/integraciones_tareas"
            redirect-url="/integraciones_tarea"
        />
    </AppLayout>
</template>
