<script setup>
import { Head } from "@inertiajs/vue3";
import { reactive, ref, watch } from "vue";
import { router } from "@inertiajs/vue3";
import axios from "axios";

import AppLayout from "@/Layouts/AppLayout.vue";
import PageHeader from "@/Components/Layout/PageHeader.vue";
import FormWrapper from "@/Components/Formulario/FormWrapper.vue";
import FormInput from "@/Components/Formulario/FormInput.vue";
import SuccessModal from "@/Components/Modal/SuccessModal.vue";

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
const modalType = ref("success");
const sending = ref(false);

// --- WATCHERS (LIMPIAN ERROR AL ESCRIBIR) ---
watch(
    () => form.nombre,
    () => (errors.nombre = null)
);

watch(
    () => form.descripcion,
    () => (errors.descripcion = null)
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
                (f) => (errors[f] = validationErrors[f][0])
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
            <!-- NOMBRE -->
            <div class="flex flex-col gap-1">
                <FormInput
                    label="Nombre"
                    v-model="form.nombre"
                    :error="errors.nombre"
                    placeholder="Nombre de la integración"
                    required
                />
                <span v-if="errors.nombre" class="text-red-600 text-sm">
                    {{ errors.nombre }}
                </span>
            </div>

            <div class="flex flex-col gap-1">
                <FormInput
                    label="Descripción"
                    type="textarea"
                    v-model="form.descripcion"
                    :error="errors.descripcion"
                    placeholder="Descripción breve"
                />
                <span v-if="errors.descripcion" class="text-red-600 text-sm">
                    {{ errors.descripcion }}
                </span>
            </div>

            <!-- ACCIONES -->
            <template #actions>
                <div class="flex justify-end gap-4 mt-6">
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
                        class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
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
