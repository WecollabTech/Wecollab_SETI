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

// --- FORMULARIO ---
const form = reactive({
    nombre: "",
    factor: 0,
    descripcion: "",
    estado: "activo",
    rubricas: "",
});

// --- ERRORES ---
const errors = reactive({
    nombre: null,
    factor: null,
    descripcion: null,
    estado: null,
    rubricas: null,
});

// --- MODAL DE ÉXITO ---
const showModal = ref(false);
const modalMessage = ref("");
const modalType = ref("success");
const sending = ref(false);

// --- RESET FORM ---
const resetForm = () => {
    form.nombre = "";
    form.factor = 0;
    form.descripcion = "";
    form.estado = "activo";
    form.rubricas = "";
    Object.keys(errors).forEach((k) => (errors[k] = null));
};

// --- HANDLE SUCCESS ---
const handleSuccess = (message) => {
    modalMessage.value = message;
    modalType.value = "success";
    showModal.value = false;
    setTimeout(() => (showModal.value = true), 50);
    resetForm();

    // Redirigir al listado
    setTimeout(() => router.get("/niveles"), 2000);
};

// --- WATCHERS PARA LIMPIAR ERRORES ---
watch(
    () => form.nombre,
    () => (errors.nombre = null)
);
watch(
    () => form.factor,
    () => (errors.factor = null)
);
watch(
    () => form.descripcion,
    () => (errors.descripcion = null)
);
watch(
    () => form.estado,
    () => (errors.estado = null)
);
watch(
    () => form.rubricas,
    () => (errors.rubricas = null)
);

const submit = async () => {
    if (sending.value) return;
    sending.value = true;
    Object.keys(errors).forEach((k) => (errors[k] = null));

    try {
        const res = await axios.post("/api/niveles", {
            ...form,
            factor: Number(form.factor), // Asegurarse de enviar número
            estado: form.estado === "activo", // <-- Convierte a true/false
        });

        if (res.data) handleSuccess("Nivel registrado correctamente");
    } catch (err) {
        if (err.response?.status === 422) {
            const validationErrors = err.response.data.errors;
            Object.keys(validationErrors).forEach(
                (f) => (errors[f] = validationErrors[f][0])
            );
        } else {
            console.error("Error al guardar:", err.message);
        }
    } finally {
        sending.value = false;
    }
};

// --- CANCEL ---
const cancel = () => router.get("/niveles");
</script>

<template>
    <Head title="Nuevo Nivel de Complejidad" />
    <AppLayout>
        <template #title>
            <PageHeader title="Nuevo Nivel de Complejidad" />
        </template>

        <FormWrapper title="Ingresa los datos del Nivel">
            <FormInput
                label="Nombre"
                v-model="form.nombre"
                :error="errors.nombre"
                placeholder="Nombre del nivel"
            />
            <p v-if="errors.nombre" class="text-red-600 text-sm mt-1">
                {{ errors.nombre }}
            </p>
            <FormInput
                label="Factor"
                v-model="form.factor"
                :error="errors.factor"
                placeholder="Factor numérico"
                type="number"
            />
            <FormInput
                label="Descripción"
                v-model="form.descripcion"
                :error="errors.descripcion"
                placeholder="Descripción del nivel"
                type="textarea"
            />
            <FormInput
                label="Rúbricas"
                v-model="form.rubricas"
                :error="errors.rubricas"
                placeholder="Ingresa las rúbricas del nivel"
                type="textarea"
            />
            <p v-if="errors.rubricas" class="text-red-600 text-sm mt-1">
                {{ errors.rubricas }}
            </p>

            <FormInput
                label="Estado"
                v-model="form.estado"
                :error="errors.estado"
                type="select"
                :options="[
                    { value: 'activo', label: 'Activo' },
                    { value: 'inactivo', label: 'Inactivo' },
                ]"
            />

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
            :auto-close="2000"
        />
    </AppLayout>
</template>
