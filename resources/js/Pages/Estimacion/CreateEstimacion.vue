<script setup>
import { ref, onMounted } from "vue";
import { Head } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import PageHeader from "@/Components/Layout/PageHeader.vue";
import PasoTipo from "./PasoTipo.vue";
import PasoFases from "./PasoFases.vue";
import PasoTareas from "./PasoTareas.vue";
import axios from "axios";
import { router } from "@inertiajs/vue3";

const paso = ref(1);
const isSaving = ref(false);

const usuariosDisponibles = ref([]);

onMounted(async () => {
    try {
        const res = await axios.get("/api/usuarios");
        usuariosDisponibles.value = res.data ?? [];
    } catch (error) {
        console.error("Error al cargar usuarios:", error);
    }
});

// Objeto principal de la estimación
const estimacion = ref({
    tipoImplementacionId: null,
    nombreTipoImplementacion: "",

    userId: null, // usuario seleccionado en PasoTipo

    nombreEmpresa: "", // ✅ nuevo
    responsable: "", // ✅ nuevo
    idNegocio: "", // ✅ nuevo

    integraciones: [],
    complejidad: null,
    comentarios: "",
    bloquesFases: [],
    bloquesIntegraciones: [],
    totalMinutos: 0,
    totalHoras: 0,
});

// Función para guardar la estimación completa
// const guardarEstimacion = async (estimacion) => {
//     if (!estimacion.complejidad?.id) {
//         console.warn(
//             "⚠️ Complejidad no definida, no se puede guardar la estimación",
//         );
//         return;
//     }

//     const payload = {
//         tipo_implementacion_id: estimacion.tipoImplementacionId,
//         nombre_tipo_implementacion: estimacion.nombreTipoImplementacion,
//         nombre_empresa: estimacion.nombreEmpresa,
//         responsable: estimacion.responsable,
//         id_negocio: estimacion.idNegocio,
//         complejidad_id: estimacion.complejidad.id,
//         comentarios: estimacion.comentarios,
//         total_minutos: estimacion.totalMinutos,
//         total_horas: estimacion.totalHoras,
//         fases: (estimacion.bloquesFases || []).map((f) => ({
//             referencia_id: f.id,
//             tareas: f.tareas.map((t) => ({
//                 tarea_id: t.tarea_id,
//                 titulo: t.titulo,
//                 duracion_minuto: t.duracion_minuto,
//             })),
//         })),
//         integraciones: (estimacion.bloquesIntegraciones || []).map((i) => ({
//             referencia_id: i.id,
//             tareas: i.tareas.map((t) => ({
//                 tarea_id: t.tarea_id,
//                 titulo: t.titulo,
//                 duracion_minuto: t.duracion_minuto,
//             })),
//         })),
//     };

//     console.log("📦 PAYLOAD FINAL:", payload);
//     await axios.post("/api/estimaciones", payload);
// };

const guardarEstimacion = async (estimacion) => {
    if (isSaving.value) return; // 🛑 evita doble envío

    if (!estimacion.complejidad?.id) {
        console.warn(
            "⚠️ Complejidad no definida, no se puede guardar la estimación",
        );
        return;
    }

    isSaving.value = true; // 🔒 bloquea

    const payload = {
        tipo_implementacion_id: estimacion.tipoImplementacionId,
        nombre_tipo_implementacion: estimacion.nombreTipoImplementacion,
        nombre_empresa: estimacion.nombreEmpresa,
        responsable: estimacion.responsable,
        id_negocio: estimacion.idNegocio,
        user_id: estimacion.userId, // <-- NUEVO
        complejidad_id: estimacion.complejidad.id,
        comentarios: estimacion.comentarios,
        total_minutos: estimacion.totalMinutos,
        total_horas: estimacion.totalHoras,

        fases: (estimacion.bloquesFases || []).map((f) => ({
            referencia_id: f.id,
            tareas: f.tareas.map((t) => ({
                tarea_id: t.tarea_id,
                titulo: t.titulo,
                duracion_minuto: t.duracion_minuto,
            })),
        })),

        integraciones: (estimacion.bloquesIntegraciones || []).map((i) => ({
            referencia_id: i.id,
            tareas: i.tareas.map((t) => ({
                tarea_id: t.tarea_id,
                titulo: t.titulo,
                duracion_minuto: t.duracion_minuto,
            })),
        })),
    };

    console.log("📦 PAYLOAD FINAL:", payload);

    try {
        await axios.post("/api/estimaciones", payload);

        // ✅ redirección Inertia
        router.visit("/estimaciones");
        // o con Ziggy:
        // router.visit(route("estimaciones.index"));
    } catch (error) {
        console.error("❌ Error al guardar:", error);
        isSaving.value = false; // 🔓 libera si falla
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
                    Object.assign(estimacion, data);
                    paso = 2;
                }
            "
        />

        <PasoFases
            v-if="paso === 2"
            :tipoImplementacionId="Number(estimacion.tipoImplementacionId)"
            :integracionesSeleccionadas="estimacion.integraciones"
            :nombreProyecto="estimacion.nombreTipoImplementacion"
            :complejidad="estimacion.complejidad"
            @back="paso = 1"
            @next="
                (data) => {
                    estimacion.bloquesFases = data.bloquesFases;
                    estimacion.bloquesIntegraciones = data.bloquesIntegraciones;
                    estimacion.totalMinutos = data.totalMinutos;
                    estimacion.totalHoras = data.totalHoras;
                    paso = 3;
                }
            "
        />

        <PasoTareas
            v-if="paso === 3"
            :estimacion="estimacion"
            :nombreProyecto="estimacion.nombreTipoImplementacion"
            :bloquesFases="estimacion.bloquesFases"
            :bloquesIntegraciones="estimacion.bloquesIntegraciones"
            :totalMinutos="estimacion.totalMinutos"
            :totalHoras="estimacion.totalHoras"
            :usuarios-disponibles="usuariosDisponibles"
            :isSaving="isSaving"
            @back="paso = 2"
            @finish="
                (data) => {
                    estimacion.bloquesFases = data.bloquesFases;
                    estimacion.bloquesIntegraciones = data.bloquesIntegraciones;
                    estimacion.totalMinutos = data.totalMinutos;
                    estimacion.totalHoras = data.totalHoras;

                    guardarEstimacion(estimacion);
                }
            "
        />
    </AppLayout>
</template>
