<script setup>
import { ref, reactive, onMounted, watch } from "vue";
import { Head, router } from "@inertiajs/vue3";
import axios from "axios";

import AppLayout from "@/Layouts/AppLayout.vue";
import PageHeader from "@/Components/Layout/PageHeader.vue";
import FormWrapper from "@/Components/Formulario/FormWrapper.vue";
import FormInput from "@/Components/Formulario/FormInput.vue";
import SuccessModal from "@/Components/Modal/SuccessModal.vue";
import Multiselect from "vue-multiselect";
import "vue-multiselect/dist/vue-multiselect.min.css";

// --- Formulario reactivo ---
const form = reactive({
    titulo: "",
    id_proceso: "",
    descripcion: "",
    estado: "pendiente",
    activo: true,
    duracion_minuto: null,
    orden: 1,
    fase_id: "",
    integraciones: [],
    fases: [],
});

// --- Errores ---
const errors = reactive({
    titulo: null,
    id_proceso: null,
    descripcion: null,
    estado: null,
    activo: null,
    duracion_minuto: null,
    orden: null,
    fase_id: null,
    integraciones: null,
    fases: null,
});

// --- Modal ---
const showModal = ref(false);
const modalMessage = ref("");
const modalType = ref("success");
const sending = ref(false);

// --- Opciones ---
const fasesOptions = ref([]);
const integracionesOptions = ref([]);

// --- Cargar datos ---
const cargarFases = async () => {
    try {
        const res = await axios.get("/api/fases");
        fasesOptions.value = res.data.data ?? [];
    } catch (err) {
        console.error("Error al cargar fases:", err);
    }
};
const cargarIntegraciones = async () => {
    try {
        const res = await axios.get("/api/integraciones");
        integracionesOptions.value = res.data.data ?? [];
    } catch (err) {
        console.error("Error al cargar integraciones:", err);
    }
};

onMounted(() => {
    cargarFases();
    cargarIntegraciones();
});

// --- Watchers para limpiar errores en tiempo real ---
watch(
    () => form.titulo,
    () => (errors.titulo = null),
);
watch(
    () => form.id_proceso,
    () => (errors.id_proceso = null),
);
watch(
    () => form.descripcion,
    () => (errors.descripcion = null),
);
watch(
    () => form.estado,
    () => (errors.estado = null),
);
watch(
    () => form.duracion_minuto,
    () => (errors.duracion_minuto = null),
);
watch(
    () => form.orden,
    () => (errors.orden = null),
);
watch(
    () => form.fase_id,
    () => (errors.fase_id = null),
);
watch(
    () => form.integraciones,
    () => (errors.integraciones = null),
);
watch(
    () => form.fases,
    () => (errors.fases = null),
);
watch(
    () => form.activo,
    () => (errors.activo = null),
);

// --- Reset Formulario ---
const resetForm = () => {
    form.titulo = "";
    form.id_proceso = "";
    form.descripcion = "";
    form.estado = "pendiente";
    form.activo = true;
    form.duracion_minuto = null;
    form.orden = 1;
    form.fase_id = "";
    form.integraciones = [];
    form.fases = [];
    Object.keys(errors).forEach((k) => (errors[k] = null));
};

// --- Submit ---
const submit = async () => {
    if (sending.value) return;

    sending.value = true;
    Object.keys(errors).forEach((k) => (errors[k] = null));

    try {
        const payload = {
            ...form,
            integraciones: form.integraciones.map((i) => i.id),
            fases: form.fases.map((f) => f.id),
        };

        const res = await axios.post("/api/tareas", payload);

        modalMessage.value =
            res.data.message || "Tarea creada correctamente ✅";
        modalType.value = "success";
        showModal.value = true;
        resetForm();
    } catch (err) {
        if (err.response?.status === 422) {
            const validationErrors = err.response.data.errors;
            Object.keys(validationErrors).forEach(
                (f) => (errors[f] = validationErrors[f][0]),
            );
        } else {
            modalMessage.value =
                err.response?.data?.message || "Error al guardar la tarea";
            modalType.value = "error";
            showModal.value = true;
        }
    } finally {
        sending.value = false;
    }
};

// --- Cancelar ---
const cancel = () => router.get("/tareas");

// --- Modal cerrar ---
const onModalClose = () => router.get("/tareas");
</script>

<template>
    <Head title="Nueva Tarea" />
    <AppLayout>
        <template #title>
            <PageHeader title="Nueva Tarea" />
        </template>

        <FormWrapper title="Ingresa los datos de la Tarea">
            <!-- GRID DE CARDS -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- CARD: Titulo -->
                <div class="card">
                    <FormInput
                        label="Título"
                        v-model="form.titulo"
                        :error="errors.titulo"
                        placeholder="Mínimo 15 caracteres"
                    />
                </div>

                <!-- CARD: ID Proceso -->
                <div class="card">
                    <FormInput
                        label="ID Proceso"
                        v-model="form.id_proceso"
                        :error="errors.id_proceso"
                        placeholder="ID del proceso"
                    />
                </div>

                <!-- CARD: Descripción -->
                <div class="card">
                    <FormInput
                        label="Descripción"
                        type="textarea"
                        v-model="form.descripcion"
                        :error="errors.descripcion"
                        placeholder="Descripción breve"
                    />
                    <p class="text-gray-400 text-sm mt-1">
                        {{ form.descripcion.length }}/1000 caracteres
                    </p>
                </div>

                <!-- CARD: Duración -->
                <div class="card">
                    <FormInput
                        label="Duración (minutos)"
                        type="number"
                        v-model="form.duracion_minuto"
                        :error="errors.duracion_minuto"
                        placeholder="Ej. 30"
                    />
                </div>

                <!-- CARD: Orden -->
                <div class="card">
                    <FormInput
                        label="Orden"
                        type="number"
                        v-model="form.orden"
                        :error="errors.orden"
                        placeholder="Ej. 1"
                    />
                </div>

                <!-- CARD: Estado -->
                <!-- <div class="card">
                    <FormInput
                        label="Estado"
                        type="select"
                        v-model="form.estado"
                        :error="errors.estado"
                        :options="[
                            { label: 'Pendiente', value: 'pendiente' },
                            { label: 'En proceso', value: 'en_proceso' },
                            { label: 'Completado', value: 'completado' },
                            { label: 'Cancelado', value: 'cancelado' },
                        ]"
                    />
                </div> -->

                <!-- CARD: Fase -->
                <div class="card">
                    <FormInput
                        label="Fase"
                        type="select"
                        v-model="form.fase_id"
                        :error="errors.fase_id"
                        :options="
                            fasesOptions.map((f) => ({
                                label: f.nombre,
                                value: f.id,
                            }))
                        "
                    />
                </div>

                <!-- CARD: Integraciones -->
                <div class="card">
                    <label class="block font-semibold mb-1"
                        >Integraciones</label
                    >
                    <Multiselect
                        v-model="form.integraciones"
                        :options="integracionesOptions"
                        track-by="id"
                        label="nombre"
                        placeholder="Selecciona integraciones"
                        multiple
                        :close-on-select="false"
                        :hide-selected="true"
                        taggable
                        class="w-full"
                    />
                    <p
                        v-if="errors.integraciones"
                        class="text-red-600 text-sm mt-1"
                    >
                        {{ errors.integraciones }}
                    </p>
                </div>

                <!-- CARD: Activo -->
                <div class="card flex flex-col justify-center items-start">
                    <span class="block font-semibold text-gray-700 mb-2"
                        >Activo</span
                    >
                    <div
                        class="w-14 h-7 rounded-full p-0.5 flex items-center transition-colors duration-300 cursor-pointer"
                        :class="form.activo ? 'bg-green-500' : 'bg-gray-300'"
                        @click="form.activo = !form.activo"
                    >
                        <div
                            class="bg-white w-6 h-6 rounded-full shadow-md transform transition-transform duration-300"
                            :class="
                                form.activo ? 'translate-x-7' : 'translate-x-0'
                            "
                        ></div>
                    </div>
                    <p v-if="errors.activo" class="text-red-600 text-sm mt-1">
                        {{ errors.activo }}
                    </p>
                </div>
            </div>

            <!-- BOTONES -->
            <template #actions>
                <div
                    class="flex flex-col md:flex-row justify-center md:justify-end gap-4 mt-6"
                >
                    <button
                        type="button"
                        @click="cancel"
                        class="px-6 py-2.5 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300"
                    >
                        Cancelar
                    </button>
                    <button
                        type="button"
                        @click="submit"
                        :disabled="sending"
                        class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Guardar
                    </button>
                </div>
            </template>
        </FormWrapper>

        <!-- MODAL -->
        <SuccessModal
            :show.sync="showModal"
            :message="modalMessage"
            :type="modalType"
            :auto-close="4000"
            @update:show="
                (val) => {
                    if (!val) onModalClose();
                }
            "
        />
    </AppLayout>
</template>

<style>
.card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    padding: 1rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    transition: box-shadow 0.3s;
}
.card:hover {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
}
</style>
