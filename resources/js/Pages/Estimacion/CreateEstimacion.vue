<script setup>
import { ref } from "vue";
import { Head } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import PageHeader from "@/Components/Layout/PageHeader.vue";
import PasoTipo from "./PasoTipo.vue";
import PasoFases from "./PasoFases.vue";
import PasoTareas from "./PasoTareas.vue";
import axios from "axios";

const paso = ref(1);

// Objeto principal de la estimación
const estimacion = ref({
    tipoImplementacionId: null,
    nombreTipoImplementacion: "",
    integraciones: [],
    complejidad: null,
    comentarios: "",
    bloques: [], // ← fases + integraciones
    totalMinutos: 0,
    totalHoras: 0,
});

// Función para guardar la estimación completa
const guardarEstimacion = async (estimacion) => {
    const payload = {
        tipo_implementacion_id: estimacion.tipoImplementacionId,
        nombre_tipo_implementacion: estimacion.nombreTipoImplementacion,
        complejidad_id: estimacion.complejidad.id,
        comentarios: estimacion.comentarios,
        total_minutos: estimacion.totalMinutos,
        total_horas: estimacion.totalHoras,
        fases: estimacion.bloques.map((b) => ({
            fase_id: b.id,
            tareas: b.tareas.map((t) => ({
                titulo: t.titulo,
                duracion_minuto: t.duracion_minuto,
            })),
        })),
    };

    await axios.post("/api/estimaciones", payload);
};
</script>

<template>
    <AppLayout>
        <Head title="Nueva Estimación" />
        <PageHeader title="Estimar horas de implementación" />

        <!-- PASO 1 -->
        <PasoTipo
            v-if="paso === 1"
            @next="
                (data) => {
                    Object.assign(estimacion, data);
                    paso = 2;
                }
            "
        />

        <!-- PASO 2 -->
        <PasoFases
            v-if="paso === 2"
            :tipoImplementacionId="Number(estimacion.tipoImplementacionId)"
            :integracionesSeleccionadas="estimacion.integraciones"
            :nombreProyecto="estimacion.nombreTipoImplementacion"
            :complejidad="estimacion.complejidad"
            @back="paso = 1"
            @next="
                (data) => {
                    estimacion.bloques = data.bloques;
                    estimacion.totalMinutos = data.totalMinutos;
                    estimacion.totalHoras = data.totalHoras;
                    paso = 3;
                }
            "
        />

        <!-- PASO 3 -->
        <PasoTareas
            v-if="paso === 3"
            :nombreProyecto="estimacion.nombreTipoImplementacion"
            :bloques="estimacion.bloques"
            :totalMinutos="estimacion.totalMinutos"
            :totalHoras="estimacion.totalHoras"
            @back="paso = 2"
            @finish="guardarEstimacion"
        />
    </AppLayout>
</template>
