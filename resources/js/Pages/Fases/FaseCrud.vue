<script setup>
import { Head } from "@inertiajs/vue3";
import { reactive, ref, watch } from "vue";
import { router } from "@inertiajs/vue3";
import axios from "axios";
import AppLayout from "@/Layouts/AppLayout.vue";
import PageHeader from "@/Components/Layout/PageHeader.vue";
import FormWrapper from "@/Components/Formulario/FormWrapper.vue";
import FormInput from "@/Components/Formulario/FormInput.vue";
import CardInput from "@/Components/Formulario/CardInput.vue";
import SuccessModal from "@/Components/Modal/SuccessModal.vue";

// --- FORMULARIO ---
const form = reactive({
    nombre: "",
    descripcion: "",
    minutos_base: 0,
});

const errors = reactive({
    nombre: null,
    descripcion: null,
    minutos_base: null,
});

const showModal = ref(false);
const modalMessage = ref("");
const modalType = ref("success");
const sending = ref(false);

const resetForm = () => {
    form.nombre = "";
    form.descripcion = "";
    form.minutos_base = 0;
    Object.keys(errors).forEach((k) => (errors[k] = null));
};

const handleSuccess = (message) => {
    modalMessage.value = message;
    modalType.value = "success";
    showModal.value = false;
    setTimeout(() => (showModal.value = true), 50);
    resetForm();
    setTimeout(() => router.get("/fases"), 4000);
};

watch(
    () => form.nombre,
    () => (errors.nombre = null),
);
watch(
    () => form.descripcion,
    () => (errors.descripcion = null),
);
watch(
    () => form.minutos_base,
    () => (errors.minutos_base = null),
);

const submit = async () => {
    if (sending.value) return;
    sending.value = true;
    Object.keys(errors).forEach((k) => (errors[k] = null));

    try {
        const res = await axios.post("/api/fases", form);
        if (res.data) handleSuccess("Fase registrada correctamente");
    } catch (err) {
        if (err.response?.status === 422) {
            const validationErrors = err.response.data.errors;
            Object.keys(validationErrors).forEach(
                (f) => (errors[f] = validationErrors[f][0]),
            );
        } else console.error("Error al guardar:", err.message);
    } finally {
        sending.value = false;
    }
};

const cancel = () => router.get("/fases");
</script>

<template>
    <Head title="Registrar nueva Fase" />

    <AppLayout>
        <template #title>
            <PageHeader title="Nueva Fase" />
        </template>

        <FormWrapper title="Ingresa los datos de la Fase">
            <!-- GRID DOS COLUMNAS -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- NOMBRE -->
                <CardInput>
                    <FormInput
                        label="Nombre"
                        v-model="form.nombre"
                        :error="errors.nombre"
                        placeholder="Nombre de la fase"
                    />
                </CardInput>

                <!-- MINUTOS BASE -->
                <CardInput>
                    <FormInput
                        label="Minutos Base"
                        type="number"
                        v-model="form.minutos_base"
                        :error="errors.minutos_base"
                        placeholder="Duración en minutos"
                    />
                </CardInput>

                <!-- DESCRIPCIÓN (OCUPA 2 COLUMNAS) -->
                <CardInput class="md:col-span-2">
                    <FormInput
                        label="Descripción"
                        type="textarea"
                        v-model="form.descripcion"
                        :error="errors.descripcion"
                        placeholder="Descripción breve"
                    />
                </CardInput>
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
                        class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                        :disabled="sending"
                    >
                        Guardar
                    </button>
                </div>
            </template>
        </FormWrapper>

        <SuccessModal
            :show.sync="showModal"
            :message="modalMessage"
            :type="modalType"
            :auto-close="4000"
        />
    </AppLayout>
</template>
