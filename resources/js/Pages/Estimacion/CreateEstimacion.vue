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
    integraciones: [], // Array de integraciones seleccionadas
    complejidad: null, // Objeto {id, nombre, factor, ...}
    comentarios: "",
    fases: [], // Array de fases con sus tareas
});

// Función para guardar la estimación completa
const guardarEstimacion = async (data) => {
    try {
        const fasesArray = data.fases?.fases || data.fases || [];

        const payload = {
            tipo_implementacion_id: data.tipoImplementacionId,
            nombre_tipo_implementacion: data.nombreTipoImplementacion,
            complejidad_id: data.complejidad.id,
            comentarios: data.comentarios,
            total_minutos: data.fases?.totalMinutos || 0,
            total_horas: data.fases?.totalHoras || 0,
            fases: fasesArray.map((fase) => ({
                fase_id: fase.id,
                minutos: fase.minutos || 0,
                horas: fase.horas || 0,
                tareas:
                    fase.tareas?.map((tarea) => ({
                        titulo: tarea.titulo,
                        duracion_minuto:
                            tarea.duracion_minuto || tarea.duracionMinuto || 0,
                    })) || [],
            })),
        };

        const response = await axios.post("/api/estimaciones", payload);
        console.log("Estimación guardada:", response.data);
    } catch (error) {
        if (error.response && error.response.status === 422) {
            console.error("Errores de validación:", error.response.data.errors);
        } else {
            console.error("Error al guardar la estimación:", error);
        }
    }
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
                    estimacion.tipoImplementacionId = data.tipoImplementacionId;
                    estimacion.nombreTipoImplementacion =
                        data.nombreTipoImplementacion;
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
            :complejidad="estimacion.complejidad"
            @back="paso = 1"
            @next="
                (data) => {
                    estimacion.fases = data;
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
            :tipoImplementacionId="estimacion.tipoImplementacionId"
            :nombreTipoImplementacion="estimacion.nombreTipoImplementacion"
            :complejidad="estimacion.complejidad"
            @back="paso = 2"
            @finish="guardarEstimacion"
        />
    </AppLayout>
</template>
