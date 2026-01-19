<script setup>
import { Head, router } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import PageHeader from "@/Components/Layout/PageHeader.vue";
import FormInput from "@/Components/Formulario/FormInput.vue";
import FormWrapper from "@/Components/Formulario/FormWrapper.vue";
import SuccessModal from "@/Components/Modal/SuccessModal.vue";
import { ref, onMounted } from "vue";
import axios from "axios";

// --- FORMULARIO ---
const form = ref({
    titulo: "",
    id_proceso: "",
    descripcion: "",
    estado: "Pendiente",
    activo: true,
    duracion_minuto: null,
    orden: null,
    fase_id: "",
});

const fases = ref([]);
const sending = ref(false);
const showModal = ref(false);
const modalMessage = ref("");
const modalType = ref("success");

// --- CARGAR FASES ---
onMounted(async () => {
    try {
        const res = await axios.get("/api/fases");
        fases.value = res.data.data ?? [];
    } catch (err) {
        console.error("Error al cargar fases:", err);
    }
});

// --- GUARDAR ---
const submit = async () => {
    sending.value = true;
    try {
        const res = await axios.post("/api/tareas", form.value);
        modalMessage.value =
            res.data.message || "Tarea registrada correctamente.";
        modalType.value = "success";
        showModal.value = true; // <-- muestra el modal
    } catch (error) {
        console.error(error.response?.data || error);
    } finally {
        sending.value = false;
    }
};

// --- CERRAR MODAL Y REDIRECCIONAR ---
const onModalClose = () => {
    router.get("/tareas"); // redirige al listado de tareas
};

// --- CANCELAR ---
const cancel = () => {
    router.get("/tareas");
};
</script>

<template>
    <Head title="Nueva Tarea" />

    <AppLayout>
        <template #title>
            <PageHeader title="Nueva Tarea" />
        </template>

        <FormWrapper title="Ingresa los datos de la Tarea" @submit="submit">
            <!-- CAMPOS -->
            <FormInput
                v-model="form.titulo"
                label="Título"
                placeholder="Título de la tarea"
            />

            <FormInput
                v-model="form.id_proceso"
                label="ID_Proceso"
                placeholder="Ingresa el Id de Proceso de la Tarea"
            />

            <FormInput
                v-model="form.descripcion"
                label="Descripción"
                type="textarea"
                placeholder="Descripción de la tarea"
            />

            <FormInput
                v-model="form.estado"
                label="Estado"
                type="select"
                :options="[
                    { label: 'Pendiente', value: 'Pendiente' },
                    { label: 'En proceso', value: 'En proceso' },
                    { label: 'Finalizada', value: 'Finalizada' },
                ]"
            />

            <FormInput
                v-model="form.duracion_minuto"
                label="Duración (minutos)"
                type="number"
                placeholder="Ej. 30"
            />
            <FormInput
                v-model="form.orden"
                label="Orden"
                type="number"
                placeholder="Ej. 1"
            />

            <FormInput
                v-model="form.fase_id"
                label="Fase"
                type="select"
                :options="fases.map((f) => ({ label: f.nombre, value: f.id }))"
            />

            <FormInput v-model="form.activo" label="Activa" type="checkbox" />

            <!-- ACCIONES -->
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

        <!-- MODAL DE ÉXITO -->
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
