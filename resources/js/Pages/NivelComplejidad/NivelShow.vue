<script setup>
import { Head } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import PageHeader from "@/Components/Layout/PageHeader.vue";
import NivelDetail from "@/Components/Show/NivelDetail.vue";

const props = defineProps({
    nivelId: { type: [Number, String], required: true },
});
</script>

<template>
    <Head title="Detalle del Nivel" />

    <AppLayout>
        <template #title>
            <PageHeader title="Detalle del Nivel" />
        </template>

        <NivelDetail :apiUrl="`/api/niveles/${props.nivelId}`" title="Nivel" />
    </AppLayout>
</template>
