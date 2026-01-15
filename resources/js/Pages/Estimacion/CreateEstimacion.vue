<script setup>
import { ref } from "vue";
import { Head } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import PageHeader from "@/Components/Layout/PageHeader.vue";
import PasoTipo from "./PasoTipo.vue";
import PasoFases from "./PasoFases.vue";
import PasoTareas from "./PasoTareas.vue";

const paso = ref(1);

const estimacion = ref({
    tipoImplementacionId: null,
    integraciones: [],
    complejidad: "",
    comentarios: "",
    fases: [],
});

// Función para guardar la estimación al finalizar
const guardarEstimacion = (data) => {
    console.log("Estimación finalizada:", data);
    alert(`Estimación para "${data.nombreTipoImplementacion}" finalizada.`);
};
</script>

<template>
    <AppLayout>
        <Head title="Nueva Estimación" />
        <PageHeader title="Estimar de horas de implementación" />
        <!-- PASO 1 -->
        <PasoTipo
            v-if="paso === 1"
            @next="
                (data) => {
                    estimacion.tipoImplementacionId = data.tipoImplementacionId;
                    estimacion.nombreTipoImplementacion =
                        data.nombreTipoImplementacion; // <- aquí
                    estimacion.integraciones = data.integraciones;
                    estimacion.complejidad = data.complejidad;
                    estimacion.comentarios = data.comentarios;
                    paso = 2;
                }
            "
        />

        <!-- PASO 2 -->
        <PasoFases
            v-if="paso === 2"
            :tipoImplementacionId="Number(estimacion.tipoImplementacionId)"
            :nombreProyecto="estimacion.nombreTipoImplementacion"
            @back="paso = 1"
            @next="
                (fases) => {
                    estimacion.fases = fases;
                    paso = 3;
                }
            "
        />

        <!-- PASO 3 -->
        <PasoTareas
            v-if="paso === 3"
            :fases="estimacion.fases.fases"
            :totalMinutos="estimacion.fases.totalMinutos"
            :totalHoras="estimacion.fases.totalHoras"
            :tiempoHMS="estimacion.fases.tiempoHMS"
            :tipoImplementacionId="estimacion.fases.tipoImplementacionId"
            :nombreTipoImplementacion="
                estimacion.fases.nombreTipoImplementacion
            "
            @back="paso = 2"
            @finish="guardarEstimacion"
        />
    </AppLayout>
</template>
