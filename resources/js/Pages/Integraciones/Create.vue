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
import ActionModal from "@/Components/Modal/ActionModal.vue";

// --- FORM ---
const form = reactive({
    nombre: "",
    descripcion: "",
});

const errors = reactive({
    nombre: null,
    descripcion: null,
});

const showModal = ref(false);
const modalMessage = ref("");
const modalType = ref("success"); // success | danger | warning | info
const sending = ref(false);

// --- WATCHERS (LIMPIAN ERROR AL ESCRIBIR) ---
watch(
    () => form.nombre,
    () => (errors.nombre = null),
);

watch(
    () => form.descripcion,
    () => (errors.descripcion = null),
);

// --- SUBMIT ---
const submit = async () => {
    if (sending.value) return;
    sending.value = true;

    Object.keys(errors).forEach((k) => (errors[k] = null));

    try {
        const res = await axios.post("/api/integraciones", form);

        if (res.data.success) {
            modalMessage.value = res.data.message;
            modalType.value = "success";

            showModal.value = false;
            setTimeout(() => (showModal.value = true), 50);

            setTimeout(() => router.get("/integraciones"), 4000);
        }
    } catch (err) {
        if (err.response?.status === 422) {
            const validationErrors = err.response.data.errors;
            Object.keys(validationErrors).forEach(
                (f) => (errors[f] = validationErrors[f][0]),
            );
        } else {
            console.error("Error al guardar:", err);
        }
    } finally {
        sending.value = false;
    }
};

const cancel = () => router.get("/integraciones");
</script>

<template>
    <Head title="Registrar Integración" />

    <AppLayout>
        <template #title>
            <PageHeader title="Nueva Integración" />
        </template>

        <FormWrapper title="Ingresa los datos de la Integración">
            <!-- GRID -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- NOMBRE -->
                <CardInput>
                    <FormInput
                        label="Nombre"
                        v-model="form.nombre"
                        :error="errors.nombre"
                        placeholder="Nombre de la integración"
                        required
                    />
                </CardInput>

                <!-- DESCRIPCIÓN (OCUPA 2 COLUMNAS) -->
                <CardInput>
                    <FormInput
                        label="Descripción"
                        type="textarea"
                        v-model="form.descripcion"
                        :error="errors.descripcion"
                        placeholder="Descripción breve"
                    />
                </CardInput>
            </div>

            <!-- ACCIONES -->
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
                        class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
                    >
                        Guardar
                    </button>
                </div>
            </template>
        </FormWrapper>

        <ActionModal
            v-model:show="showModal"
            :type="modalType"
            title="¡Integración creada!"
            :message="modalMessage"
            confirmText="Aceptar"
            :showCancel="false"
            @confirm="showModal = false"
        />
    </AppLayout>
</template>
