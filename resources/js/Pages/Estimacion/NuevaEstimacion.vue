<script setup>
import { ref, watch, onMounted } from "vue";
import axios from "axios";
import { Head, router } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import PageHeader from "@/Components/Layout/PageHeader.vue";
import FormWrapper from "@/Components/Formulario/FormWrapper.vue";
import FormEstimacion from "@/Components/Formulario/FormEstimacion.vue";
import SuccessModal from "@/Components/Modal/SuccessModal.vue";
import Multiselect from "vue-multiselect";
import "vue-multiselect/dist/vue-multiselect.min.css";

// --- FORMULARIO ---
const estimacion = ref({
    tipoImplementacion: "",
    fecha: "",
    complejidad: "",
    integraciones: [],
    comentarios: "",
});

// --- LISTAS ---
const tiposImplementacion = ref([]);
const integracionesDisponibles = ref([]);
const sending = ref(false);

// --- MODAL ---
const showModal = ref(false);
const modalMessage = ref("Estimación registrada correctamente.");

// --- Cargar tipos de implementación ---
onMounted(async () => {
    try {
        const resTipos = await axios.get("/api/tipoimplementacion");
        tiposImplementacion.value = resTipos.data.data ?? [];
    } catch (err) {
        console.error("Error cargando tipos de implementación:", err);
    }
});

// --- Watch para cargar integraciones según tipo seleccionado ---
watch(
    () => estimacion.value.tipoImplementacion,
    async (nuevoTipoId) => {
        integracionesDisponibles.value = [];
        estimacion.value.integraciones = [];

        if (!nuevoTipoId) return;

        try {
            const res = await axios.get(
                `/api/tipoimplementacion/${nuevoTipoId}/integraciones`
            );
            integracionesDisponibles.value = res.data.data ?? [];
        } catch (err) {
            console.error("Error cargando integraciones:", err);
        }
    }
);

// --- Guardar ---
const guardar = async () => {
    if (sending.value) return;
    sending.value = true;

    try {
        await axios.post("/api/estimaciones", estimacion.value);

        // Mostrar modal de éxito
        showModal.value = true;

        // Limpiar formulario
        estimacion.value = {
            tipoImplementacion: "",
            fecha: "",
            complejidad: "",
            integraciones: [],
            comentarios: "",
        };

        // Redirigir después de 2s
        setTimeout(() => router.get("/estimaciones"), 2000);
    } catch (err) {
        console.error(err.response?.data || err);
    } finally {
        sending.value = false;
    }
};

// --- Cancelar ---
const cancel = () => router.get("/estimaciones");
</script>

<template>
    <AppLayout>
        <Head title="Nueva Estimación" />
        <PageHeader title="Estimar de horas de implementación" />

        <FormWrapper
            @submit.prevent="guardar"
            title="Para poder realizar la estimacion es necesario llenar el formulario"
        >
            <!-- Tipo de implementación -->
            <div class="flex flex-col">
                <!-- Select -->
                <FormEstimacion
                    label="Tipo de implementación"
                    type="select"
                    :options="
                        tiposImplementacion.map((t) => ({
                            value: t.id,
                            label: t.nombre,
                        }))
                    "
                    v-model="estimacion.tipoImplementacion"
                />

                <!-- Sub-label o info -->
                <div v-if="estimacion.tipoImplementacion" class="mt-1">
                    <span class="text-gray-500 text-xs font-medium"
                        >Rubrica de la Implementación:</span
                    >
                    <p class="text-gray-600 text-sm ml-1">
                        {{
                            tiposImplementacion.find(
                                (t) =>
                                    String(t.id) ===
                                    String(estimacion.tipoImplementacion)
                            )?.descripcion || ""
                        }}
                    </p>
                </div>
            </div>

            <!-- Fecha -->
            <!-- <FormEstimacion
                label="Fecha"
                type="date"
                v-model="estimacion.fecha"
            /> -->

            <!-- Integraciones dependientes del tipo de implementación -->
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Integraciones
                </label>

                <!-- Si hay un tipo seleccionado, mostrar checkboxes -->
                <div v-if="estimacion.tipoImplementacion">
                    <FormEstimacion
                        type="checkbox"
                        :options="
                            integracionesDisponibles.map((i) => ({
                                value: i.id,
                                label: i.nombre,
                            }))
                        "
                        v-model="estimacion.integraciones"
                    />
                </div>

                <!-- Mensaje informativo dentro del mismo bloque si no hay tipo seleccionado -->
                <p v-else class="text-gray-400 text-sm italic">
                    Selecciona primero un tipo de implementación para ver las
                    integraciones disponibles.
                </p>
            </div>

            <!-- Nivel de complejidad -->
            <div class="mt-1">
                <label class="block text-gray-700 font-medium mb-1">
                    Nivel de complejidad
                </label>
                <div class="flex gap-2">
                    <button
                        v-for="nivel in ['Baja', 'Media', 'Alta']"
                        :key="nivel"
                        type="button"
                        @click="estimacion.complejidad = nivel"
                        :class="[
                            'w-24 h-10 rounded-lg text-sm font-semibold flex items-center justify-center transition',
                            estimacion.complejidad === nivel
                                ? nivel === 'Baja'
                                    ? 'bg-green-500 text-white border border-green-500'
                                    : nivel === 'Media'
                                    ? 'bg-yellow-400 text-white border border-yellow-400'
                                    : 'bg-red-500 text-white border border-red-500'
                                : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-100',
                        ]"
                    >
                        {{ nivel }}
                    </button>
                </div>
            </div>

            <!-- Comentarios -->
            <FormEstimacion
                label="Comentarios"
                type="textarea"
                v-model="estimacion.comentarios"
            />

            <!-- Botones -->
            <template #actions>
                <div
                    class="flex flex-col md:flex-row justify-center md:justify-end gap-4"
                >
                    <button
                        type="button"
                        @click="cancel"
                        class="px-6 py-2.5 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                        :disabled="sending"
                    >
                        Guardar
                    </button>
                </div>
            </template>
        </FormWrapper>

        <!-- Modal de éxito -->
        <SuccessModal
            :show.sync="showModal"
            :message="modalMessage"
            :type="'success'"
            :auto-close="2000"
        />
    </AppLayout>
</template>
